{ft_include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/icon_report_builder.png" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      {$L.module_name}
    </td>
  </tr>
  </table>

  {ft_include file='messages.tpl'}

  {if !$export_manager_available}
    <div class="error margin_bottom_large">
      <div style="padding: 6px">
        {$L.text_export_manager_not_available}
      </div>
    </div>
  {/if}

  <div class="margin_bottom_large">
    {$L.text_module_intro}
  </div>

  <form action="{$same_page}" method="post">

    <div class="subtitle underline margin_bottom_large">{$LANG.word_settings}</div>

	  <table cellspacing="0" cellpadding="1" class="list_table">
	  <tr>
	    <td width="300" class="pad_left_small"><label for="setting1a">{$L.phrase_show_reports_icon}</label></td>
	    <td>
	      <input type="radio" name="show_reports_icon_on_submission_listing_page" id="setting1a" value="yes"
	        {if $module_settings.show_reports_icon_on_submission_listing_page == "yes"}checked="checked"{/if} />
	        <label for="setting1a">{$LANG.word_yes}</label>
	      <input type="radio" name="show_reports_icon_on_submission_listing_page" id="setting1b" value="no"
	        {if $module_settings.show_reports_icon_on_submission_listing_page == "no"}checked="checked"{/if} />
	        <label for="setting1b">{$LANG.word_no}</label>
	    </td>
	  </tr>
	  <tr>
	    <td class="pad_left_small"><label for="ib1">{$L.phrase_icon_behaviour}</label></td>
	    <td>
	      <input type="radio" name="icon_behaviour" id="ib1" value="dialog"
	        {if $module_settings.icon_behaviour == "dialog"}checked="checked"{/if} />
	        <label for="ib1">Dialog</label>
	      <input type="radio" name="icon_behaviour" id="ib2" value="window"
	        {if $module_settings.icon_behaviour == "window"}checked="checked"{/if} />
	        <label for="ib2">New window</label>
	    </td>
	  </tr>
	  <tr>
	    <td class="pad_left_small"><label for="ed1">{$L.phrase_expand_reports_desc}</label></td>
	    <td>
	      <input type="radio" name="expand_by_default" id="ed1" value="yes"
	        {if $module_settings.expand_by_default == "yes"}checked="checked"{/if} />
	        <label for="ed1">{$LANG.word_yes}</label>
	      <input type="radio" name="expand_by_default" id="ed2" value="no"
	        {if $module_settings.expand_by_default == "no"}checked="checked"{/if} />
	        <label for="ed2">{$LANG.word_no}</label>
	    </td>
	  </tr>
	  </table>

    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
    </p>

  </form>

{ft_include file='modules_footer.tpl'}
