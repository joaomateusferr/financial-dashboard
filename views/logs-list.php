<style>
    body:has(.log-details-shell) { overflow: auto; }
    .app:has(.log-details-shell) { height: auto; min-height: 100vh; }
    .main:has(.log-details-shell) { min-height: 100vh; }
    .option:has(.log-details-shell) { overflow: visible; }
    .log-details-shell { align-items: stretch; min-height: 100%; box-sizing: border-box; }
    .log-details-page { display: flex; flex-direction: column; width: calc(100% + 48px); margin: 0 -24px; }
    .log-details-intro { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin: 0 24px 28px; }
    .log-details-eyebrow { margin: 0 0 8px; color: var(--primary); font-size: .75rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .log-details-heading { margin: 0; color: var(--text-color); font-size: clamp(1.45rem, 2vw, 1.85rem); letter-spacing: -.025em; }
    .log-details-summary { margin: 8px 0 0; color: color-mix(in srgb, var(--text-color) 64%, transparent); font-size: .92rem; }
    .log-details-context { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 999px; color: color-mix(in srgb, var(--text-color) 68%, transparent); font-size: .78rem; font-weight: 650; white-space: nowrap; }
    .log-details-context::before { width: 7px; height: 7px; border-radius: 50%; background: var(--primary); content: ""; }
    .log-details-panel { margin: 0 8px; overflow: hidden; border: 1px solid color-mix(in srgb, var(--border-color) 88%, var(--text-color)); border-radius: 10px; background: var(--sidebar-bg); }
    .log-details-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 20px; border-bottom: 1px solid var(--border-color); background: color-mix(in srgb, var(--bg-color) 96%, var(--sidebar-bg)); }
    .log-details-toolbar-title { margin: 0; color: var(--text-color); font-size: .9rem; font-weight: 750; }
    .log-details-toolbar-meta { color: color-mix(in srgb, var(--text-color) 54%, transparent); font-size: .78rem; }
    .log-details-table-wrap { overflow-x: auto; }
    .log-details-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .log-details-table th { padding: 15px 20px 11px; color: color-mix(in srgb, var(--text-color) 53%, transparent); font-size: .7rem; font-weight: 800; letter-spacing: .08em; text-align: left; text-transform: uppercase; }
    .log-details-table th:first-child { width: 170px; }
    .log-details-table th:last-child { width: 180px; }
    .log-details-table td { padding: 18px 20px; border-top: 1px solid var(--border-color); vertical-align: top; }
    .log-details-date { color: color-mix(in srgb, var(--text-color) 70%, transparent); font-size: .82rem; font-variant-numeric: tabular-nums; line-height: 1.55; white-space: nowrap; }
    .log-details-date span { display: block; color: color-mix(in srgb, var(--text-color) 48%, transparent); font-size: .76rem; }
    .log-details-message { min-width: 280px; color: var(--text-color); font-size: .86rem; line-height: 1.55; overflow-wrap: anywhere; white-space: pre-line; }
    .log-details-actions { display: flex; flex-wrap: wrap; gap: 6px 12px; margin-top: 7px; }
    .log-details-action { padding: 0; border: 0; background: transparent; color: var(--primary); font: inherit; font-size: .76rem; font-weight: 700; cursor: pointer; }
    .log-details-action:hover { color: var(--primary-hover); text-decoration: underline; text-underline-offset: 3px; }
    .log-details-action + .log-details-action { padding-left: 12px; border-left: 1px solid var(--border-color); }
    .log-details-backtrace { display: none; margin-top: 14px; padding: 12px 14px; overflow-x: auto; border: 1px solid var(--border-color); border-radius: 8px; background: color-mix(in srgb, var(--bg-color) 92%, var(--sidebar-bg)); color: color-mix(in srgb, var(--text-color) 72%, transparent); font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .75rem; line-height: 1.65; white-space: pre; }
    .log-details-backtrace.is-visible { display: block; }
    .log-details-extra { display: none; flex-direction: column; gap: 10px; margin-top: 14px; padding: 14px; border: 1px solid var(--border-color); border-radius: 8px; background: color-mix(in srgb, var(--bg-color) 92%, var(--sidebar-bg)); }
    .log-details-extra.is-visible { display: flex; }
    .log-details-extra-item { display: grid; grid-template-columns: 120px minmax(0, 1fr); align-items: baseline; gap: 12px; }
    .log-details-extra-label { color: color-mix(in srgb, var(--text-color) 48%, transparent); font-size: .68rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .log-details-extra-value { color: color-mix(in srgb, var(--text-color) 78%, transparent); font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .75rem; overflow-wrap: anywhere; }
    .log-details-expanded-row { display: none; }
    .log-details-expanded-row.is-visible { display: table-row; }
    .log-details-table tbody tr:has(+ .log-details-expanded-row) td { padding-bottom: 3px; }
    .log-details-expanded-cell { padding: 0 20px 18px !important; border-top: 0 !important; }
    .log-details-expanded-content { display: grid; gap: 14px; width: 100%; }
    .log-details-expanded-content .log-details-backtrace, .log-details-expanded-content .log-details-extra { margin-top: 0; }
    .log-details-host { color: color-mix(in srgb, var(--text-color) 72%, transparent); font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .78rem; overflow-wrap: anywhere; }
    .log-details-footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 15px 20px; border-top: 1px solid var(--border-color); color: color-mix(in srgb, var(--text-color) 52%, transparent); font-size: .76rem; }
    .log-details-pagination { display: flex; align-items: center; gap: 8px; }
    .log-details-page-button { min-height: 32px; padding: .42rem .72rem; border: 1px solid var(--border-color); border-radius: 7px; background: var(--sidebar-bg); color: color-mix(in srgb, var(--text-color) 75%, transparent); font: inherit; font-size: .76rem; font-weight: 700; cursor: pointer; transition: border-color .2s, color .2s, background .2s; }
    .log-details-page-button:hover { border-color: var(--primary); background: var(--hover-bg); color: var(--primary); }
    .log-details-page-button.is-disabled { opacity: .45; pointer-events: none; cursor: not-allowed; }
    @media (max-width: 720px) {
        .log-details-intro { align-items: flex-start; flex-direction: column; gap: 14px; }
        .log-details-table { min-width: 720px; }
    }
    @media (max-width: 520px) {
        .log-details-footer { align-items: flex-start; flex-direction: column; }
        .log-details-extra-item { grid-template-columns: 1fr; gap: 4px; }
    }
</style>

<section class="page-shell log-details-shell">
    <div class="log-details-page">
        <div class="log-details-intro">
            <div>
                <p class="log-details-eyebrow">System observability</p>
                <h2 class="log-details-heading">Log entries</h2>
                <p class="log-details-summary">Detailed records from the selected application log.</p>
            </div>
            <div class="log-details-context">api-exceptions</div>
        </div>

        <div class="log-details-panel">
            <div class="log-details-toolbar">
                <p class="log-details-toolbar-title">Recent entries</p>
                <span class="log-details-toolbar-meta">Showing 5 records</span>
            </div>

            <div class="log-details-table-wrap">
                <table class="log-details-table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Message</th>
                            <th scope="col">Host</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="log-details-date"><time datetime="2026-10-19T14:42:18-03:00">2026-10-19</time><span>14:42:18</span></td>
                            <td class="log-details-message">Uncaught RuntimeException: Unable to complete request processing. The upstream service returned an unexpected response. bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbcccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc
                                <div class="log-details-actions"><button class="log-details-action js-view-backtrace" type="button" aria-expanded="false">View Backtrace</button><button class="log-details-action js-view-details" type="button" aria-expanded="false">View Details</button></div>
                                <div class="log-details-backtrace">#0 /app/src/Services/ApiClient.php(184): ApiClient-&gt;request()
#1 /app/src/Controllers/TransactionController.php(72): ApiClient-&gt;syncTransactions() aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
#2 /app/public/index.php(38): TransactionController-&gt;handle()
#3 {main}</div>
                                <div class="log-details-extra">
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">ERROR</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a91c2</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div>
                                </div>
                            </td>
                            <td class="log-details-host">api-server-01</td>
                        </tr>
                        <tr class="log-details-expanded-row"><td class="log-details-expanded-cell" colspan="3"><div class="log-details-expanded-content"><div class="log-details-backtrace">#0 /app/src/Services/ApiClient.php(184): ApiClient-&gt;request()
#1 /app/src/Controllers/TransactionController.php(72): ApiClient-&gt;syncTransactions()
#2 /app/public/index.php(38): TransactionController-&gt;handle()
#3 {main}</div><div class="log-details-extra"><div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">ERROR</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a91c2</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div></div></div></td></tr>
                        <tr>
                            <td class="log-details-date"><time datetime="2026-10-19T14:39:06-03:00">2026-10-19</time><span>14:39:06</span></td>
                            <td class="log-details-message">Request timeout while connecting to the transaction provider after 30 seconds.
                                <div class="log-details-actions"><button class="log-details-action js-view-backtrace" type="button" aria-expanded="false">View Backtrace</button><button class="log-details-action js-view-details" type="button" aria-expanded="false">View Details</button></div>
                                <div class="log-details-backtrace">#0 /app/src/Services/TransactionProvider.php(96): TransactionProvider-&gt;connect()
#1 /app/src/Services/TransactionSync.php(41): TransactionProvider-&gt;fetch()
#2 /app/src/Jobs/SyncTransactionsJob.php(29): TransactionSync-&gt;run()
#3 {main}</div>
                                <div class="log-details-extra">
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">ERROR</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a8db4</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div>
                                </div>
                            </td>
                            <td class="log-details-host">worker-queue-02</td>
                        </tr>
                        <tr class="log-details-expanded-row"><td class="log-details-expanded-cell" colspan="3"><div class="log-details-expanded-content"><div class="log-details-backtrace">#0 /app/src/Services/TransactionProvider.php(96): TransactionProvider-&gt;connect()
#1 /app/src/Services/TransactionSync.php(41): TransactionProvider-&gt;fetch()
#2 /app/src/Jobs/SyncTransactionsJob.php(29): SyncTransactionsJob-&gt;run()
#3 {main}</div><div class="log-details-extra"><div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">ERROR</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a8db4</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div></div></div></td></tr>
                        <tr>
                            <td class="log-details-date"><time datetime="2026-10-19T14:31:44-03:00">2026-10-19</time><span>14:31:44</span></td>
                            <td class="log-details-message">Invalid response payload received while synchronizing account data. Field "transaction_id" was missing from the response.
                                <div class="log-details-actions"><button class="log-details-action js-view-backtrace" type="button" aria-expanded="false">View Backtrace</button><button class="log-details-action js-view-details" type="button" aria-expanded="false">View Details</button></div>
                                <div class="log-details-backtrace">#0 /app/src/Services/AccountSync.php(118): AccountSync-&gt;validatePayload()
#1 /app/src/Controllers/AccountController.php(64): AccountSync-&gt;sync()
#2 /app/public/index.php(38): AccountController-&gt;update()
#3 {main}</div>
                                <div class="log-details-extra">
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">WARNING</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a7ca8</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div>
                                </div>
                            </td>
                            <td class="log-details-host">api-server-02</td>
                        </tr>
                        <tr class="log-details-expanded-row"><td class="log-details-expanded-cell" colspan="3"><div class="log-details-expanded-content"><div class="log-details-backtrace">#0 /app/src/Services/AccountSync.php(118): AccountSync-&gt;validatePayload()
#1 /app/src/Controllers/AccountController.php(64): AccountSync-&gt;sync()
#2 /app/public/index.php(38): AccountController-&gt;update()
#3 {main}</div><div class="log-details-extra"><div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">WARNING</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a7ca8</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div></div></div></td></tr>
                        <tr>
                            <td class="log-details-date"><time datetime="2026-10-19T14:18:52-03:00">2026-10-19</time><span>14:18:52</span></td>
                            <td class="log-details-message">Failed to persist categorized transaction. A database constraint prevented the operation from being completed.
                                <div class="log-details-actions"><button class="log-details-action js-view-backtrace" type="button" aria-expanded="false">View Backtrace</button><button class="log-details-action js-view-details" type="button" aria-expanded="false">View Details</button></div>
                                <div class="log-details-backtrace">#0 /app/src/Repositories/TransactionRepository.php(207): PDOStatement-&gt;execute()
#1 /app/src/Services/TransactionService.php(151): TransactionRepository-&gt;save()
#2 /app/src/Controllers/TransactionController.php(91): TransactionService-&gt;store()
#3 {main}</div>
                                <div class="log-details-extra">
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">ERROR</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a5e10</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div>
                                </div>
                            </td>
                            <td class="log-details-host">database-01</td>
                        </tr>
                        <tr class="log-details-expanded-row"><td class="log-details-expanded-cell" colspan="3"><div class="log-details-expanded-content"><div class="log-details-backtrace">#0 /app/src/Repositories/TransactionRepository.php(207): PDOStatement-&gt;execute()
#1 /app/src/Services/TransactionService.php(151): TransactionRepository-&gt;save()
#2 /app/src/Controllers/TransactionController.php(91): TransactionService-&gt;store()
#3 {main}</div><div class="log-details-extra"><div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">ERROR</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a5e10</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div></div></div></td></tr>
                        <tr>
                            <td class="log-details-date"><time datetime="2026-10-19T13:57:20-03:00">2026-10-19</time><span>13:57:20</span></td>
                            <td class="log-details-message">Authentication token could not be refreshed because the identity provider was temporarily unavailable.
                                <div class="log-details-actions"><button class="log-details-action js-view-backtrace" type="button" aria-expanded="false">View Backtrace</button><button class="log-details-action js-view-details" type="button" aria-expanded="false">View Details</button></div>
                                <div class="log-details-backtrace">#0 /app/src/Services/IdentityProvider.php(82): IdentityProvider-&gt;refreshToken()
#1 /app/src/Middlewares/AuthMiddleware.php(44): IdentityProvider-&gt;authenticate()
#2 /app/public/index.php(24): AuthMiddleware-&gt;handle()
#3 {main}</div>
                                <div class="log-details-extra">
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">WARNING</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a4a76</span></div>
                                    <div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div>
                                </div>
                            </td>
                            <td class="log-details-host">auth-server-01</td>
                        </tr>
                        <tr class="log-details-expanded-row"><td class="log-details-expanded-cell" colspan="3"><div class="log-details-expanded-content"><div class="log-details-backtrace">#0 /app/src/Services/IdentityProvider.php(82): IdentityProvider-&gt;refreshToken()
#1 /app/src/Middlewares/AuthMiddleware.php(44): IdentityProvider-&gt;authenticate()
#2 /app/public/index.php(24): AuthMiddleware-&gt;handle()
#3 {main}</div><div class="log-details-extra"><div class="log-details-extra-item"><span class="log-details-extra-label">Level</span><span class="log-details-extra-value">WARNING</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Request ID</span><span class="log-details-extra-value">req_7f3a4a76</span></div><div class="log-details-extra-item"><span class="log-details-extra-label">Environment</span><span class="log-details-extra-value">production</span></div></div></div></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="log-details-footer">
                <span>Sorted by date · newest entries first</span>
                <nav class="log-details-pagination" aria-label="Log pages">
                    <button class="log-details-page-button is-disabled" type="button">Previous</button>
                    <button class="log-details-page-button" type="button">Next</button>
                </nav>
            </div>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.js-view-backtrace').forEach(function (Button) {
        Button.addEventListener('click', function () {
            const ExpandedRow = this.closest('tr').nextElementSibling;
            const Backtrace = ExpandedRow.querySelector('.log-details-backtrace');
            const IsVisible = Backtrace.classList.toggle('is-visible');

            ExpandedRow.classList.toggle('is-visible', ExpandedRow.querySelector('.is-visible') !== null);
            this.setAttribute('aria-expanded', String(IsVisible));
            this.textContent = IsVisible ? 'Hide Backtrace' : 'View Backtrace';
        });
    });

    document.querySelectorAll('.js-view-details').forEach(function (Button) {
        Button.addEventListener('click', function () {
            const ExpandedRow = this.closest('tr').nextElementSibling;
            const Details = ExpandedRow.querySelector('.log-details-extra');
            const IsVisible = Details.classList.toggle('is-visible');

            ExpandedRow.classList.toggle('is-visible', ExpandedRow.querySelector('.is-visible') !== null);
            this.setAttribute('aria-expanded', String(IsVisible));
            this.textContent = IsVisible ? 'Hide Details' : 'View Details';
        });
    });
</script>
