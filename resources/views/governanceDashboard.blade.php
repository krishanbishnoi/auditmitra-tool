
<style>
    .dashboard-title-wrap {
        min-width: 150px;
    }

    .dashboard-title {
        font-weight: 1.5rem;
        color: rgb(177, 114, 72);
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .header-actions form {
            width: 100%;
        }

        .header-actions select,
        .header-actions input {
            flex: 1 1 auto;
        }
    }
</style>

<div class="card dashboard-header-clean shadow-sm border-0 mb-4">
    <div class="card-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

            {{-- Title --}}
            <div class="dashboard-title-wrap">
                <h2 class="dashboard-title mb-0">
                    Governance Dashboard
                </h2>
            </div>

            {{-- Actions --}}
            <div class="header-actions">
                <form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-0">

                    {{-- Client selector --}}
                    <select id="client-select" name="client" class="form-control form-select" style="min-width: 180px;">
                        <option value="">Select Client</option>
                        <option value="1">Client1</option>
                        <option value="2">Client2</option>
                        <option value="3">Client3</option>
                        <option value="4">Client4</option>
                        <option value="5">Client5</option>
                        <option value="6">Client6</option>
                        <option value="7">Client7</option>
                        <option value="8">Client8</option>
                    </select>


            {{-- Date range --}}
            <div id="custom-date-range" class="d-flex align-items-center gap-2">
                <label class="mb-0">Start Date*</label>
                <input type="text" id="start-date" name="start_date" value="" placeholder="Start Date"
                    class="form-control flatpickr" style="width: 150px;">

                <label class="mb-0">End Date*</label>
                <input type="text" id="end-date" name="end_date" value="" placeholder="End Date"
                    class="form-control flatpickr" style="width: 150px;">
            </div>

            {{-- Download button --}}
            <button type="submit" class="btn btn-primary text-nowrap">
                Download
            </button>
            </form>

</div>
</div>

 {{-- Stat cards row --}}
              <div class="d-flex stat-cards-row gap-20 mb-4">
            @foreach ([
                'Audits Scoped(MTD)' => 1125,
                'Achieved Audits' => 1074,
                'Achievement' => 89,
                'Overall Score' => 779,
                'Action Planning' => 9,
            ] as $label => $value)
                <div class="flex-fill" style="min-width: 0;">
                    <div class="card cardboxInner h-100" style="background-color: rgb(245, 198, 176);">
                        <div class="card-body text-center">
                            <div class="mb-1">
                                <strong style="color:rgb(226, 80, 12) !important;">{{ $label }}</strong>
                            </div>
                            <div class="stat-text">
                                <span class="count2">{{ $value }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
           {{-- end stat cards row --}}
