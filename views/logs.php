<?php

use App\Services\Log;

$MostRecentLogDate = Log::getMostRecentLogDate();

$Level = !empty($_GET['Level']) ? (int) $_GET['Level'] : 100;
$Host = !empty($_GET['Host']) ? $_GET['Host'] : '';
$Identifier = !empty($_GET['Identifier']) ? $_GET['Identifier'] : '';
$Message = !empty($_GET['Message']) ? $_GET['Message'] : '';

$Date = !empty($_GET['Date']) ? $_GET['Date'] : '';

if(empty($Date)){

    if(!empty($MostRecentLogDate))
        $Date = $MostRecentLogDate;
    else
        $Date = date('Y-m-d');

}

$Logs = Log::search($Date, $Host, $Identifier, $Level, $Message);
$Filter = http_build_query(['Date' => $Date, 'Host' => $Host, 'Identifier' => $Identifier, 'Level' => $Level, 'Message' => $Message]);
?>

<style>
    body:has(.logs-shell) { overflow: auto; }
    .app:has(.logs-shell) { height: auto; min-height: 100vh; }
    .main:has(.logs-shell) { min-height: 100vh; }
    .option:has(.logs-shell) { overflow: visible; }
    .logs-shell { align-items: stretch; min-height: 100%; box-sizing: border-box; }
    .logs-page { display: flex; flex-direction: column; width: calc(100% + 48px); margin: 0 -24px; }
    .logs-intro { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin: 0 24px 28px; }
    .logs-eyebrow { margin: 0 0 8px; color: var(--primary); font-size: .75rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .logs-heading { margin: 0; color: var(--text-color); font-size: clamp(1.45rem, 2vw, 1.85rem); letter-spacing: -.025em; }
    .logs-summary { margin: 8px 0 0; color: color-mix(in srgb, var(--text-color) 64%, transparent); font-size: .92rem; }
    .logs-status { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 999px; color: color-mix(in srgb, var(--text-color) 68%, transparent); font-size: .78rem; font-weight: 650; white-space: nowrap; }
    .logs-status::before { width: 7px; height: 7px; border-radius: 50%; background: #33a36b; content: ""; }
    .logs-panel { margin: 0 8px; overflow: hidden; border: 1px solid color-mix(in srgb, var(--border-color) 88%, var(--text-color)); border-radius: 10px; background: var(--sidebar-bg); }
    .logs-filters { position: sticky; top: 0; z-index: 2; display: grid; grid-template-columns: 1fr 1.15fr 1.15fr .9fr 1.45fr auto; align-items: end; gap: 14px; padding: 20px; border-bottom: 1px solid var(--border-color); background: color-mix(in srgb, var(--bg-color) 96%, var(--sidebar-bg)); }
    .logs-field { display: grid; gap: 7px; }
    .logs-field label { color: color-mix(in srgb, var(--text-color) 75%, transparent); font-size: .75rem; font-weight: 750; letter-spacing: .02em; }
    .logs-field input, .logs-field select { width: 100%; min-height: 42px; padding: .65rem .78rem; border: 1px solid var(--border-color); border-radius: 9px; background: var(--sidebar-bg); color: var(--text-color); font: inherit; font-size: .88rem; transition: border-color .2s, box-shadow .2s; }
    .logs-field input::placeholder { color: color-mix(in srgb, var(--text-color) 42%, transparent); }
    .logs-field select { appearance: none; padding-right: 2.5rem; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px; }
    .logs-field input:focus, .logs-field select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 12%, transparent); }
    .logs-search { min-height: 42px; padding: .65rem 1rem; border: 1px solid var(--primary); border-radius: 9px; background: var(--primary); color: #fff; font: inherit; font-size: .88rem; font-weight: 700; white-space: nowrap; cursor: pointer; transition: background .2s, border-color .2s; }
    .logs-search:hover { border-color: var(--primary-hover); background: var(--primary-hover); }
    .logs-list-header, .log-row { display: grid; grid-template-columns: minmax(220px, 1fr) 150px 180px; align-items: center; gap: 18px; }
    .logs-list-header { position: sticky; top: 0; z-index: 1; padding: 15px 20px 11px; color: color-mix(in srgb, var(--text-color) 53%, transparent); font-size: .7rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; background: var(--sidebar-bg); }
    .log-row { min-height: 70px; padding: 13px 20px; border-top: 1px solid var(--border-color); transition: background .2s; }
    .log-row:hover { background: var(--hover-bg); }
    .log-identifier { width: fit-content; padding: 0; border: 0; background: transparent; color: var(--primary); font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .88rem; font-weight: 700; text-decoration: none; cursor: pointer; }
    .log-identifier:hover { color: var(--primary-hover); text-decoration: underline; text-underline-offset: 3px; }
    .log-count { color: var(--text-color); font-size: .86rem; font-variant-numeric: tabular-nums; }
    .log-count span { color: color-mix(in srgb, var(--text-color) 54%, transparent); }
    .log-date { color: color-mix(in srgb, var(--text-color) 70%, transparent); font-size: .82rem; font-variant-numeric: tabular-nums; }
    .logs-list-footer { padding: 15px 20px; border-top: 1px solid var(--border-color); color: color-mix(in srgb, var(--text-color) 52%, transparent); font-size: .76rem; }
    @media (max-width: 900px) { .logs-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); } .logs-search { width: 100%; } }
    @media (max-width: 620px) { .logs-intro { align-items: flex-start; flex-direction: column; gap: 14px; } .logs-filters { grid-template-columns: 1fr; } .logs-list-header { display: none; } .log-row { grid-template-columns: 1fr auto; gap: 8px 14px; } .log-identifier { grid-column: 1 / -1; } .log-date { text-align: right; } }
</style>

<section class="page-shell logs-shell">
    <div class="logs-page">
        <div class="logs-intro">
            <div>
                <p class="logs-eyebrow">System observability</p>
                <h2 class="logs-heading">Log consultation</h2>
                <p class="logs-summary">Search, review and access your application log groups.</p>
            </div>
            <div class="logs-status">Log service available</div>
        </div>

        <div class="logs-panel">
            <form class="logs-filters" aria-label="Log filters" method="get">

                <div class="logs-field">
                    <label for="Date">Date</label>
                    <input id="Date" name="Date" value="<?php echo $Date ?>" type="text" inputmode="numeric" maxlength="10" placeholder="YYYY-MM-DD" autocomplete="off">
                </div>
                <div class="logs-field">
                    <label for="Host">Host</label>
                    <input id="Host" name="Host" value="<?php echo $Host ?>" type="text" placeholder="server-name">
                </div>
                <div class="logs-field">
                    <label for="Identifier">Identifier</label>
                    <input id="Identifier" name="Identifier" value="<?php echo $Identifier ?>" type="text" placeholder="identifier">
                </div>
                <div class="logs-field">
                    <label for="Level">Level</label>
                    <select id="Level" name="Level">

                        <?php

                            foreach(Log::getLogLevels() as $Value => $Name){

                                $Selected = $Level == $Value ? 'selected' : '';
                                echo "<option value=\"$Value\" $Selected>$Name</option>";

                            }

                        ?>

                    </select>
                </div>
                <div class="logs-field">
                    <label for="Message">Message</label>
                    <input id="Message" name="Message" value="<?php echo $Message ?>" type="search" placeholder="Search in log messages">
                </div>
                <button class="logs-search" type="submit">Search logs</button>
            </form>

            <div class="logs-list-header" aria-hidden="true"><span>Log identifier</span><span>Entries</span><span>Last modified</span></div>

            <div class="logs-list" aria-label="Log identifiers">

                <?php

                foreach($Logs as $Identifier => $Lines){ 

                    $DateTime = new DateTimeImmutable($Lines[0]['datetime']);
                    $Date = $DateTime->format('Y-m-d');
                    $Time = $DateTime->format('H:i:s');
                    
                    ?>

                    <div class="log-row">
                        <button onclick="window.location.href='/logs-list?<?php echo $Filter ?>';" class="log-identifier" type="button"><?php echo $Identifier ?></button>
                        <div class="log-count">
                            <?php echo count($Lines) ?>
                        </div>
                        <time class="log-date"> <?php echo $Date.' - '.$Time?></time>
                    </div>

                    <?php

                } ?>
                
            </div>

            <div class="logs-list-footer">Showing 5 log identifiers · Sorted by last modified</div>
        </div>
    </div>
</section>

<script>
    const LogDateField = document.getElementById('log-date');

    LogDateField.addEventListener('input', function () {
        const Digits = this.value.replace(/\D/g, '').slice(0, 8);
        const Year = Digits.slice(0, 4);
        const Month = Digits.slice(4, 6);
        const Day = Digits.slice(6, 8);

        this.value = Year;

        if (Month) {
            this.value += `-${Month}`;
        }

        if (Day) {
            this.value += `-${Day}`;
        }
    });
</script>
