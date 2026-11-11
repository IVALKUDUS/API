{{--
    Foto profil + nama user (tampilan sama seperti di Kelola User).

    Pemakaian:
        <x-user-avatar :user="$peminjaman->user" />
        <x-user-avatar :user="$log->user" name="Sistem" />
        <x-user-avatar :user="$item->user">
            <div class="text-xs text-gray-500 font-normal">{{ $item->user->no_hp ?? '-' }}</div>
        </x-user-avatar>

    Props:
        user : instance App\Models\User (boleh null)
        name : nama cadangan jika user null / tidak punya nama
        size : kelas ukuran avatar (default w-10 h-10)
    Slot (opsional): info tambahan di bawah nama (mis. no. HP).
--}}
@props(['user' => null, 'name' => null, 'size' => 'w-10 h-10'])

@php
    $nama = $user->name ?? $name ?? '-';
@endphp

<div class="flex items-center gap-3">
    @if($user && $user->foto_profile)
        <img src="{{ asset('storage/' . $user->foto_profile) }}"
             alt="{{ $nama }}"
             class="{{ $size }} rounded-full object-cover border border-gray-200 flex-shrink-0">
    @else
        <div class="{{ $size }} rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold text-xs uppercase flex-shrink-0 border border-gray-200">
            {{ mb_substr($nama, 0, 2) }}
        </div>
    @endif

    <div class="min-w-0">
        <span class="text-gray-900 font-semibold">{{ $nama }}</span>
        @if(trim((string) $slot) !== '')
            {{ $slot }}
        @endif
    </div>
</div>
