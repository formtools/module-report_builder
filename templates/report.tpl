  {if $is_single_form && !$is_valid_single_form}
    <div class="margin_bottom_large notify">
      <div style="padding: 6px">
        {$LANG.report_builder.notify_no_form_found}
      </div>
    </div>
  {else}

    {if !$is_single_form}
      <div class="margin_bottom_large">
        <div id="rb_expand_contract">{$LANG.report_builder.phrase_expand_all}</div>
        <div id="rb_expand_label" class="hidden">{$LANG.report_builder.phrase_expand_all}</div>
        <div id="rb_contract_label" class="hidden">{$LANG.report_builder.phrase_contract_all}</div>
        {$LANG.report_builder.text_reports_intro}
      </div>
    {/if}

    {foreach from=$forms item=form_info name=row}
      {assign var='index' value=$smarty.foreach.row.index}
      {assign var='count' value=$smarty.foreach.row.iteration}
      {assign var='form_id' value=$form_info.form_id}
      {assign var=key value="form_`$form_id`"}

      <div class="rb_form_section">
        <div class="rb_form_section_heading">
          <div class="rb_num_reports">
            {if $form_views.$key|@count == 0}
              0 {$LANG.report_builder.word_reports}
            {elseif $form_views.$key|@count == 1}
              1 {$LANG.report_builder.word_report}
            {else}
              {$form_views.$key|@count} {$LANG.report_builder.word_reports}
            {/if}
          </div>
          <div class="rb_form_name">{$form_info.form_name}</div>
          <div class="clear"></div>
        </div>
        <div class="{if $is_single_form}rb_report_section_single_form{else}rb_report_section{/if}">
          {foreach from=$form_views.$key item=view_info}
            <div class="rb_view_row">
              <div class="rb_view_name">{$view_info.view_name}</div>
              <div class="rb_export_options">
                <div class="rb_num_submissions">{$LANG.report_builder.word_results_c} {$view_info.num_submissions_in_view}</div>
                {foreach from=$export_options item=export_option}
                  {assign var=group_id value=$export_option.group_id}
                  <div class="rb_export_option">
                    <a href="{$g_root_url}/modules/report_builder/report.php?export_group_id={$group_id}&export_type_id={$export_option.export_type_id}&export_group_{$group_id}_results=all&form_id={$form_id}&view_id={$view_info.view_id}"
                      target="_blank"><img src="{$g_root_url}/modules/export_manager/images/icons/{$export_option.icon}"
                      title="{$export_option.group_name|escape} - {$export_option.export_type_name|escape}" /></a>
                  </div>
                {/foreach}
              </div>
              <div class="clear"></div>
            </div>
          {/foreach}
        </div>
      </div>
    {/foreach}
  {/if}
