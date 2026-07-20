<?php

use App\Models\Category;
use App\Models\Employee;
use App\Models\Food;
use App\Models\FoodAssignment;
use App\Models\FoodPriceChange;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\PageSeeder;

// CMS Management Tests
test('hero slides can be created and ordered', function () {
    $slide1 = HeroSlide::factory()->create(['ordering' => 2]);
    $slide2 = HeroSlide::factory()->create(['ordering' => 1]);

    $slides = HeroSlide::ordered()->get();

    expect($slides->first()->id)->toBe($slide2->id)
        ->and($slides->last()->id)->toBe($slide1->id);
});

test('active hero slides can be filtered', function () {
    HeroSlide::factory()->create(['status' => 'active']);
    HeroSlide::factory()->create(['status' => 'inactive']);

    $activeSlides = HeroSlide::active()->get();

    expect($activeSlides)->toHaveCount(1)
        ->and($activeSlides->first()->status)->toBe('active');
});

test('pages are seeded correctly', function () {
    $this->seed([PageSeeder::class]);

    expect(Page::count())->toBe(4)
        ->and(Page::where('slug', 'about')->exists())->toBeTrue()
        ->and(Page::where('slug', 'contact')->exists())->toBeTrue()
        ->and(Page::where('slug', 'gallery')->exists())->toBeTrue()
        ->and(Page::where('slug', 'testimonials')->exists())->toBeTrue();
});

test('page meta data is stored as json', function () {
    $page = Page::factory()->create([
        'meta_data' => ['meta_title' => 'Test', 'meta_description' => 'Test description'],
    ]);

    expect($page->meta_data)->toBeArray()
        ->and($page->meta_data['meta_title'])->toBe('Test');
});

test('site settings can be stored and retrieved', function () {
    SiteSetting::set('test_key', 'test_value');

    $value = SiteSetting::get('test_key');

    expect($value)->toBe('test_value');
});

test('site settings are cached', function () {
    SiteSetting::set('cached_key', 'cached_value');

    $value1 = SiteSetting::get('cached_key');
    $value2 = SiteSetting::get('cached_key');

    expect($value1)->toBe('cached_value')
        ->and($value2)->toBe('cached_value');
});

// Food Category Management Tests
test('category belongs to a creator', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['created_by' => $user->id]);

    expect($category->creator->id)->toBe($user->id);
});

test('category slug is auto-generated from name', function () {
    $category = Category::factory()->create(['name' => 'Test Category', 'slug' => '']);

    expect($category->slug)->toBe('test-category');
});

test('category has many foods', function () {
    $category = Category::factory()->create();
    $food1 = Food::factory()->create(['category_id' => $category->id]);
    $food2 = Food::factory()->create(['category_id' => $category->id]);

    expect($category->foods)->toHaveCount(2);
});

test('active categories can be filtered', function () {
    Category::factory()->create(['status' => 'active']);
    Category::factory()->create(['status' => 'inactive']);

    $activeCategories = Category::active()->get();

    expect($activeCategories)->toHaveCount(1);
});

// Food Menu Management Tests
test('food belongs to category', function () {
    $category = Category::factory()->create();
    $food = Food::factory()->create(['category_id' => $category->id]);

    expect($food->category->id)->toBe($category->id);
});

test('food has effective price attribute', function () {
    $food = Food::factory()->create([
        'price' => 100,
        'discount_price' => 80,
    ]);

    expect($food->effective_price)->toBe(80.0);
});

test('food without discount returns regular price', function () {
    $food = Food::factory()->create([
        'price' => 100,
        'discount_price' => null,
    ]);

    expect($food->effective_price)->toBe(100.0);
});

test('food has discount method works correctly', function () {
    $foodWithDiscount = Food::factory()->create([
        'price' => 100,
        'discount_price' => 80,
    ]);

    $foodWithoutDiscount = Food::factory()->create([
        'price' => 100,
        'discount_price' => null,
    ]);

    expect($foodWithDiscount->hasDiscount())->toBeTrue()
        ->and($foodWithoutDiscount->hasDiscount())->toBeFalse();
});

test('active and available foods can be filtered', function () {
    Food::factory()->create(['status' => 'active', 'availability' => true]);
    Food::factory()->create(['status' => 'inactive', 'availability' => true]);
    Food::factory()->create(['status' => 'active', 'availability' => false]);

    $activeFoods = Food::active()->get();
    $availableFoods = Food::available()->get();

    expect($activeFoods)->toHaveCount(2)
        ->and($availableFoods)->toHaveCount(2);
});

// Price Approval System Tests
test('food price change can be created', function () {
    $food = Food::factory()->create(['price' => 100]);
    $user = User::factory()->create();

    $priceChange = FoodPriceChange::factory()->create([
        'food_id' => $food->id,
        'old_price' => 100,
        'new_price' => 120,
        'requested_by' => $user->id,
        'status' => 'pending',
    ]);

    expect($priceChange->food->id)->toBe($food->id)
        ->and($priceChange->status)->toBe('pending');
});

test('price change approval updates food price', function () {
    $food = Food::factory()->create(['price' => 100]);
    $requester = User::factory()->create();
    $approver = User::factory()->create();

    $priceChange = FoodPriceChange::factory()->create([
        'food_id' => $food->id,
        'old_price' => 100,
        'new_price' => 120,
        'requested_by' => $requester->id,
        'status' => 'pending',
    ]);

    $priceChange->approve($approver);

    expect($priceChange->fresh()->status)->toBe('approved')
        ->and($priceChange->fresh()->approved_by)->toBe($approver->id)
        ->and((float) $food->fresh()->price)->toBe(120.0);
});

test('price change rejection stores reason', function () {
    $food = Food::factory()->create(['price' => 100]);
    $requester = User::factory()->create();
    $approver = User::factory()->create();

    $priceChange = FoodPriceChange::factory()->create([
        'food_id' => $food->id,
        'old_price' => 100,
        'new_price' => 120,
        'requested_by' => $requester->id,
        'status' => 'pending',
    ]);

    $priceChange->reject($approver, 'Price increase too high');

    expect($priceChange->fresh()->status)->toBe('rejected')
        ->and($priceChange->fresh()->rejection_reason)->toBe('Price increase too high')
        ->and((float) $food->fresh()->price)->toBe(100.0);
});

test('pending price changes can be filtered', function () {
    FoodPriceChange::factory()->create(['status' => 'pending']);
    FoodPriceChange::factory()->create(['status' => 'approved']);
    FoodPriceChange::factory()->create(['status' => 'rejected']);

    $pending = FoodPriceChange::pending()->get();

    expect($pending)->toHaveCount(1);
});

// Food Assignment Tests
test('food can be assigned to employee', function () {
    $food = Food::factory()->create();
    $employee = Employee::factory()->create();
    $assigner = User::factory()->create();

    $assignment = FoodAssignment::factory()->create([
        'food_id' => $food->id,
        'employee_id' => $employee->id,
        'assigned_by' => $assigner->id,
    ]);

    expect($assignment->food->id)->toBe($food->id)
        ->and($assignment->employee->id)->toBe($employee->id)
        ->and($assignment->assigner->id)->toBe($assigner->id);
});

test('food belongs to many employees through assignments', function () {
    $food = Food::factory()->create();
    $employee1 = Employee::factory()->create();
    $employee2 = Employee::factory()->create();
    $assigner = User::factory()->create();

    FoodAssignment::factory()->create([
        'food_id' => $food->id,
        'employee_id' => $employee1->id,
        'assigned_by' => $assigner->id,
    ]);

    FoodAssignment::factory()->create([
        'food_id' => $food->id,
        'employee_id' => $employee2->id,
        'assigned_by' => $assigner->id,
    ]);

    expect($food->employees)->toHaveCount(2);
});

test('active food assignments can be filtered', function () {
    FoodAssignment::factory()->create(['status' => 'active']);
    FoodAssignment::factory()->create(['status' => 'inactive']);

    $activeAssignments = FoodAssignment::active()->get();

    expect($activeAssignments)->toHaveCount(1);
});
