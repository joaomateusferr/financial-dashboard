<?php

use App\Services\Log;

$Level = !empty($_GET['Level']) ? (int) $_GET['Level'] : 100;
$Host = !empty($_GET['Host']) ? $_GET['Host'] : '';
$Identifier = !empty($_GET['Identifier']) ? $_GET['Identifier'] : '';
$Message = !empty($_GET['Message']) ? $_GET['Message'] : '';
$Date = !empty($_GET['Date']) ? $_GET['Date'] : '';

$Logs = Log::search($Date, $Host, $Identifier, $Level, $Message);
$Logs = isset($Logs[$Identifier]) ? $Logs[$Identifier] : [];

?>
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
            <div class="log-details-context"><?php echo $Identifier; ?></div>
        </div>

        <div class="log-details-panel">
            <div class="log-details-toolbar">
                <p class="log-details-toolbar-title">Recent entries</p>
                <span class="log-details-toolbar-meta">Showing <?php echo count($Logs); ?> records</span>
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
                        <?php if(empty($Logs)) { ?>
                            <tr>
                                <td class="log-details-message" colspan="3">No log entries found for the selected filters.</td>
                            </tr>
                        <?php } ?>

                        <?php foreach($Logs as $Index => $Log) {

                            $DateTime = new DateTimeImmutable($Log['datetime']);
                            $Date = $DateTime->format('Y-m-d');
                            $Time = $DateTime->format('H:i:s');

                            $Backtrace = '';

                            if(!empty($Log['context']['stack_trace'])){ //prioritize the context trace if it exists

                                foreach($Log['context']['stack_trace'] as $Line){
                                    $Backtrace .= $Line."<br>";
                                }

                                unset($Logs[$Index]['context']['stack_trace']);
                                unset($Logs[$Index]['extra']['backtrace']);

                            }elseif(!empty($Log['extra']['backtrace'])){

                                foreach($Log['extra']['backtrace'] as $Line){
                                    $Backtrace .= $Line."<br>";
                                }

                                unset($Logs[$Index]['extra']['backtrace']);

                            }

                            ?>
                            <tr>
                                <td class="log-details-date">
                                    <time datetime="<?php echo $Log['datetime']; ?>"><?php echo $Date; ?></time>
                                    <span><?php echo $Time; ?></span>
                                </td>

                                <td class="log-details-message"><?php echo $Log['message']; ?>
                                    <div class="log-details-actions">
                                        <?php if(!empty($Backtrace)): ?><button class="log-details-action js-view-backtrace" type="button" aria-expanded="false">View Backtrace</button><?php endif; ?>
                                        <button class="log-details-action js-view-details" type="button" aria-expanded="false">View Details</button>
                                    </div>
                                </td>
                                <td class="log-details-host"><?php echo !empty($Log['extra']['hostname']) ? $Log['extra']['hostname'] : '-'; ?></td>
                            </tr>
                            <tr class="log-details-expanded-row">
                                <td class="log-details-expanded-cell" colspan="3">
                                    <div class="log-details-expanded-content">
                                        <?php if(!empty($Backtrace)){ ?>
                                            <div class="log-details-backtrace"><?php echo $Backtrace; ?></div>
                                        <?php } ?>
                                        <div class="log-details-extra">

                                            <div class="log-details-extra-item">
                                                <span class="log-details-extra-label"><?php echo 'Level'; ?></span>
                                                <span class="log-details-extra-value"><?php echo $Logs[$Index]['level_name'].' ('.$Logs[$Index]['level'].')'; ?></span>
                                            </div>

                                            <?php foreach($Logs[$Index]['context'] as $Key => $Value) {

                                                $Value = is_array($Value) ? '<pre>'.json_encode($Value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre>' : $Value;

                                                ?>

                                                <div class="log-details-extra-item">
                                                    <span class="log-details-extra-label"><?php echo $Key; ?></span>
                                                    <span class="log-details-extra-value"><?php echo $Value; ?></span>
                                                </div>

                                            <?php } ?>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="log-details-footer">
                <span><?php echo $Date; ?> · Sorted by date · newest entries first</span>
                <!--<nav class="log-details-pagination" aria-label="Log pages">
                    <button class="log-details-page-button is-disabled" type="button">Previous</button>
                    <button class="log-details-page-button" type="button">Next</button>
                </nav>-->
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
