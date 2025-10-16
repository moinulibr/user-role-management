<x-admin-layout>
    <x-slot name="page_title">Role Management</x-slot>

    <div class="rms-container">
        <div class="rms-header">
            <h2 class="rms-title">Roles Management</h2>
            <button id="rms-btn-create" class="rms-btn rms-btn-primary">+ Create Role</button>
        </div>

        <div id="rms-table-container">Loading roles...</div>
    </div>

    <div class="rms-modal" id="rms-modal">
        <div class="rms-modal-content" id="rms-modal-content"></div>
    </div>

@push('styles')
<style>
    .rms-container{background:#fff;padding:20px;border-radius:10px;margin-top:20px}
    .rms-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
    .rms-btn{padding:8px 14px;border:none;border-radius:4px;cursor:pointer}
    .rms-btn-primary{background:#007bff;color:#fff}
    .rms-btn-edit{background:#ffc107;color:#000}
    .rms-btn-delete{background:#dc3545;color:#fff}
    .rms-table{width:100%;border-collapse:collapse}
    .rms-table th,.rms-table td{border:1px solid #ddd;padding:8px;text-align:left}
    .rms-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;z-index:999}
    .rms-modal-content{background:white;border-radius:8px;width:85%;max-height:90vh;overflow:auto;padding:20px}
    .rms-close-btn{float:right;background:red;color:#fff;border:none;border-radius:4px;padding:4px 10px;cursor:pointer}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('rms-modal');
    const modalContent = document.getElementById('rms-modal-content');
    const tableContainer = document.getElementById('rms-table-container');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const openModal = html => { modalContent.innerHTML = html; modal.style.display = 'flex'; };
    const closeModal = () => { modal.style.display = 'none'; modalContent.innerHTML = ''; };
    window.onclick = e => { if (e.target === modal) closeModal(); };
    window.rmsCloseModal = closeModal;

    const loadTable = () => {
        fetch('{{ route('admin.roles.table') }}')
            .then(r => r.text())
            .then(html => tableContainer.innerHTML = html);
    };
    loadTable();

    document.getElementById('rms-btn-create').onclick = () => {
        fetch('{{ route('admin.roles.create') }}')
            .then(r => r.text()).then(openModal);
    };

    window.rmsEditRole = id => {
        fetch(`/admin/roles/${id}/edit`).then(r => r.text()).then(openModal);
    };

    window.rmsDeleteRole = id => {
        if (!confirm('Are you sure?')) return;
        fetch(`/admin/roles/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':csrf}})
            .then(() => loadTable());
    };
});
</script>
@endpush
</x-admin-layout>
