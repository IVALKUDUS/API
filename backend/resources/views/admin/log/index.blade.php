@extends('layouts.app')

@section('title', 'Log Aktivitas - Panel Admin')
@section('header_title', 'Log Aktivitas Sistem')

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:justify-between md:items-center gap-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Riwayat Aktivitas</h3>
                <span class="text-xs text-gray-500">Total {{ $logs->total() }} catatan</span>
            </div>

            <form action="{{ route('admin.log.index') }}" method="GET" class="flex">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari user / aktivitas..."
                    class="px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-gray-900 rounded-r-lg hover:bg-gray-800">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 border-b w-12">No</th>
                        <th class="py-3 px-4 border-b">Waktu</th>
                        <th class="py-3 px-4 border-b">User</th>
                        <th class="py-3 px-4 border-b">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-gray-500">{{ $logs->firstItem() + $loop->index }}</td>
                            <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">
                                {{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $log->user->name ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-4">{{ $log->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection