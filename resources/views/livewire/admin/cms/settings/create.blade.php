<?php
use function Livewire\Volt\{layout};
layout('components.layouts.admin');
?>

<div>
    <div class="p-6">
        @php header('Location: '.route('admin.cms.settings.index')); exit; @endphp
    </div>
</div>
