@extends('layouts.master')

@section('content')

<style>
    :root {
        --cp-primary:       #4f46e5;
        --cp-primary-dark:  #4338ca;
        --cp-primary-light: #eef2ff;
        --cp-primary-mid:   #c7d2fe;
        --cp-success:       #059669;
        --cp-success-light: #ecfdf5;
        --cp-success-mid:   #6ee7b7;
        --cp-danger:        #dc2626;
        --cp-danger-light:  #fef2f2;
        --cp-danger-mid:    #fecaca;
        --cp-gray-50:       #f9fafb;
        --cp-gray-100:      #f3f4f6;
        --cp-gray-200:      #e5e7eb;
        --cp-gray-400:      #9ca3af;
        --cp-gray-600:      #6b7280;
        --cp-gray-800:      #374151;
        --cp-gray-900:      #1a1a2e;
    }

    .cp-page { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem; }

    .cp-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }
    .cp-page-header h1 { font-size: 20px; font-weight: 700; color: var(--cp-gray-900); margin: 0 0 3px; }
    .cp-page-header p  { font-size: 13px; color: var(--cp-gray-600); margin: 0; }

    .cp-btn-add {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600;
        padding: 8px 16px; border-radius: 8px; border: none;
        background: var(--cp-primary); color: #fff;
        text-decoration: none; cursor: pointer; transition: background .15s;
        white-space: nowrap;
    }
    .cp-btn-add:hover { background: var(--cp-primary-dark); color: #fff; text-decoration: none; }

    .cp-card {
        background: #fff;
        border: 1px solid var(--cp-gray-200);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .cp-card-section { padding: 1.5rem; }

    .cp-section-label-green {
        display: inline-flex; align-items: center;
        font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--cp-success);
        background: var(--cp-success-light);
        border: 1px solid var(--cp-success-mid);
        border-radius: 20px;
        padding: 3px 10px;
        margin-bottom: 1.1rem;
    }

    /* Search bar */
    .cp-table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .cp-search-wrap { position: relative; }
    .cp-search-wrap input {
        height: 34px;
        padding: 0 10px 0 32px;
        font-size: 13px;
        border: 1px solid var(--cp-gray-200);
        border-radius: 8px;
        color: var(--cp-gray-800);
        background: var(--cp-gray-50);
        width: 220px;
        transition: border-color .15s, box-shadow .15s;
    }
    .cp-search-wrap input:focus {
        outline: none;
        border-color: var(--cp-primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        background: #fff;
    }
    .cp-search-wrap .cp-search-icon {
        position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
        color: var(--cp-gray-400); font-size: 13px; pointer-events: none;
    }
    .cp-row-count { font-size: 12px; color: var(--cp-gray-400); }

    /* Table */
    .cp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .cp-table thead tr { background: var(--cp-gray-50); border-bottom: 1px solid var(--cp-gray-200); }
    .cp-table thead th {
        padding: 10px 14px; font-weight: 600; color: var(--cp-gray-600);
        text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em;
        white-space: nowrap; cursor: pointer; user-select: none;
    }
    .cp-table thead th:hover { color: var(--cp-primary); }
    .cp-table tbody tr { border-bottom: 1px solid var(--cp-gray-100); transition: background .1s; }
    .cp-table tbody tr:hover { background: var(--cp-gray-50); }
    .cp-table tbody tr:last-child { border-bottom: none; }
    .cp-table tbody td { padding: 10px 14px; color: var(--cp-gray-800); vertical-align: middle; }

    .cp-badge-gray {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: var(--cp-gray-100); color: var(--cp-gray-600);
        border: 1px solid var(--cp-gray-200);
    }
    .cp-badge-param {
        display: inline-flex; align-items: center;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 500;
        background: var(--cp-primary-light); color: var(--cp-primary-dark);
        border: 1px solid var(--cp-primary-mid);
    }

    .cp-empty { text-align: center; padding: 2.5rem; color: var(--cp-gray-400); font-size: 13px; }

    /* Pagination */
    .cp-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--cp-gray-100);
        font-size: 12px;
        color: var(--cp-gray-600);
    }
    .cp-pagination-btns { display: flex; gap: 4px; }
    .cp-pg-btn {
        width: 30px; height: 30px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 6px; border: 1px solid var(--cp-gray-200);
        background: #fff; font-size: 12px; font-weight: 500;
        color: var(--cp-gray-600); cursor: pointer; transition: all .15s;
    }
    .cp-pg-btn:hover { background: var(--cp-primary-light); border-color: var(--cp-primary-mid); color: var(--cp-primary); }
    .cp-pg-btn.active { background: var(--cp-primary); border-color: var(--cp-primary); color: #fff; }
    .cp-pg-btn:disabled { opacity: .4; cursor: not-allowed; }
</style>

<div class="cp-page">

    <div class="cp-page-header">
        <div>
            <h1>Sub-parameters</h1>
            <p>All sub-parameters grouped by parent parameter</p>
        </div>
        <a href="{{ route('sub-parameters.create') }}" class="cp-btn-add">+ Add sub-parameter</a>
    </div>

    <div class="cp-card">
        <div class="cp-card-section">
            <div class="cp-section-label-green">All sub-parameters</div>

            <div class="cp-table-toolbar">
                <div class="cp-search-wrap">
                    <span class="cp-search-icon">&#128269;</span>
                    <input type="text" id="cp-search" placeholder="Search...">
                </div>
                <span class="cp-row-count" id="cp-row-count"></span>
            </div>

            <table class="cp-table" id="sub-parameter-table">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Parameter</th>
                        <th>Sub-parameter name</th>
                    </tr>
                </thead>
                <tbody id="cp-tbody">
                    @forelse($subParameters as $index => $sub)
                        <tr>
                            <td><span class="cp-badge-gray">{{ $index + 1 }}</span></td>
                            <td><span class="cp-badge-param">{{ $sub->parameter->name ?? 'N/A' }}</span></td>
                            <td>{{ $sub->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="cp-empty">No sub-parameters found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="cp-pagination">
            <span id="cp-pagination-info"></span>
            <div class="cp-pagination-btns" id="cp-pagination-btns"></div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsPerPage = 15;
    const tbody      = document.getElementById('cp-tbody');
    const searchInput= document.getElementById('cp-search');
    const rowCount   = document.getElementById('cp-row-count');
    const pgInfo     = document.getElementById('cp-pagination-info');
    const pgBtns     = document.getElementById('cp-pagination-btns');

    let allRows   = Array.from(tbody.querySelectorAll('tr'));
    let filtered  = [...allRows];
    let curPage   = 1;

    function render() {
        const start = (curPage - 1) * rowsPerPage;
        const end   = start + rowsPerPage;

        allRows.forEach(r => r.style.display = 'none');
        filtered.slice(start, end).forEach(r => r.style.display = '');

        rowCount.textContent = filtered.length + ' row' + (filtered.length !== 1 ? 's' : '');
        pgInfo.textContent   = 'Showing ' + Math.min(start + 1, filtered.length) + '–' + Math.min(end, filtered.length) + ' of ' + filtered.length;

        const totalPages = Math.ceil(filtered.length / rowsPerPage);
        pgBtns.innerHTML = '';

        const prev = document.createElement('button');
        prev.className = 'cp-pg-btn';
        prev.textContent = '‹';
        prev.disabled = curPage === 1;
        prev.onclick = () => { curPage--; render(); };
        pgBtns.appendChild(prev);

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = 'cp-pg-btn' + (i === curPage ? ' active' : '');
            btn.textContent = i;
            btn.onclick = (p => () => { curPage = p; render(); })(i);
            pgBtns.appendChild(btn);
        }

        const next = document.createElement('button');
        next.className = 'cp-pg-btn';
        next.textContent = '›';
        next.disabled = curPage === totalPages || totalPages === 0;
        next.onclick = () => { curPage++; render(); };
        pgBtns.appendChild(next);
    }

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        filtered = allRows.filter(r => r.textContent.toLowerCase().includes(q));
        curPage  = 1;
        render();
    });

    render();
});
</script>

@endsection