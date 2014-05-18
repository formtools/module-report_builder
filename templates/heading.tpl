  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><img src="{$g_root_url}/modules/report_builder/images/icon_report_builder.png" border="0" width="34" height="34" /></td>
    <td class="title">
      {if $is_valid_single_form}
        <a href="reports.php">{$LANG.report_builder.word_reports}</a>
        <span class="joiner">&raquo;</span>
        {$current_form_name}
      {else}
        {$LANG.report_builder.word_reports}
      {/if}
    </td>
  </tr>
  </table>
