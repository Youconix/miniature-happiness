<div id="maintenance">
    <h1>Systeem onderhoud</h1>

	<div class="adminPanel">
    <table>
        <tr>
            <td class="bold"><a href="javascript:adminMaintenance.compressCSS()">{compressCSS}</a></td>
            <td id="css_compress" class="maintenanceReady">{ready}</td>
            <td><br/></td>
            <td class="bold"><a href="javascript:adminMaintenance.compressJS()">{compressJS}</a></td>
            <td id="js_compress" class="maintenanceReady">{ready}</td>
        </tr>
        <tr>
            <td colspan="5"><br/></td>
        </tr>
        <tr>
            <td class="bold"><a href="javascript:adminMaintenance.checkDatabase()">{checkDatabase}</a></td>
            <td id="check_database" class="maintenanceReady">{ready}</td>
            <td><br/></td>
            <td class="bold"><a href="javascript:adminMaintenance.optimizeDatabase()">{optimizeDatabase}</a></td>
            <td id="optimize_database" class="maintenanceReady">{ready}</td>
        </tr>
        <tr>
            <td colspan="5"><br/></td>
        </tr>
        <tr>
            <td class="bold"><a href="javascript:adminMaintenance.cleanLogs()">{cleanLogs}</a></td>
            <td id="clean_logs" class="maintenanceReady">{ready}</td>
            <td><br/></td>            
            <td class="bold"><a href="javascript:adminMaintenance.cleanStatsMonth()">{cleanStatsMonth}</a></td>
            <td id="clean_stats_month" class="maintenanceReady">{ready}</td>
        </tr>
        <tr>
            <td colspan="5"><br/></td>
        </tr>
        <tr>
            <td class="bold"><a href="javascript:adminMaintenance.cleanStatsYear()">{cleanStatsYear}</a></td>
            <td id="clean_stats_year" class="maintenanceReady">{ready}</td>
            <td><br/></td>
            <td class="bold"><a href="javascript:adminSoftware.updateList()">{systemUpdate}</a></td>
            <td><br/></td>
        </tr>
    </table>
    </div>
</div>
