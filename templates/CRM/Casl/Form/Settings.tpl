{* HEADER *}

{*
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>
*}

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT)
{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}
*}

  <div class="crm-section">
    <div class="label">{$form.grant_consent.label}</div>
    <div class="content">{$form.grant_consent.html}
      <p class="description">{ts}Generally the CASL Support extension is meant to SET the no-bulk-email condition, and (usually) never to UNSET it. Here you can specify if it should ever be automatically unset (i.e. allow the contact to receive mailings again).{/ts}</p>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.autofill.label}</div>
    <div class="content">{$form.autofill.html}
      <p class="description">{ts}This allows you to specify the ONLY conditions in which the CASL Support extension will AUTOMATICALLY populate the CASL fields for your contacts. In any other conditions you will have to manually update the CASL fields yourself, either using a manual process, a separate configuration (such as in a Drupal Webform, or custom PHP code.{/ts}</p>
    </div>
    <div class="clear"></div>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
