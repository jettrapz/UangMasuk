@props(['type' => 'info', 'message'])

@if($type === 'success')
<div class="gate-error" style="color:#35b3a3;" role="alert">{{ $message }}</div>
@elseif($type === 'error')
<div class="gate-error" role="alert">{{ $message }}</div>
@else
<div class="gate-error" role="alert">{{ $message }}</div>
@endif
