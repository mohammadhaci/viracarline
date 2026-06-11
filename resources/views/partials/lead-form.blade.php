@if(session('lead_submitted'))
    <div class="rounded-lg bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
        {{ __('Your inquiry has been sent. We will get back to you shortly.') }}
    </div>
@else
    <form method="POST" action="{{ route('leads.store') }}" enctype="multipart/form-data" class="grid gap-4">
        @csrf
        <input type="hidden" name="type" value="{{ $type ?? 'contact' }}">
        @isset($vehicle)
            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
        @endisset
        <input type="text" name="website" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="grid gap-1 text-sm font-medium">
                {{ __('Name') }} *
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="rounded-lg border-zinc-300 text-sm focus:border-zinc-900 focus:ring-zinc-900">
            </label>
            <label class="grid gap-1 text-sm font-medium">
                {{ __('Email') }} *
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="rounded-lg border-zinc-300 text-sm focus:border-zinc-900 focus:ring-zinc-900">
            </label>
        </div>
        <label class="grid gap-1 text-sm font-medium">
            {{ __('Phone') }}
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="rounded-lg border-zinc-300 text-sm focus:border-zinc-900 focus:ring-zinc-900">
        </label>
        <label class="grid gap-1 text-sm font-medium">
            {{ __('Message') }} *
            <textarea name="message" rows="4" required
                      class="rounded-lg border-zinc-300 text-sm focus:border-zinc-900 focus:ring-zinc-900">{{ old('message') }}</textarea>
        </label>
        @if($withPhotos ?? false)
            <label class="grid gap-1 text-sm font-medium">
                {{ __('Photos') }}
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="text-sm">
            </label>
        @endif

        @if($errors->any())
            <ul class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <button type="submit" class="justify-self-start rounded-lg bg-zinc-950 px-7 py-3 text-sm font-semibold text-white hover:bg-zinc-800">
            {{ __('Send inquiry') }}
        </button>
    </form>
@endif
