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
    <div class="label">{$form.ignore_null.label}</div>
    <div class="content">{$form.ignore_null.html}
      <p class="description">{ts}Under normal conditions, if a new contact would be created with no CASL data filled in, it would automatically be flagged to not get mail since there would be no consent. If you DON'T want this default behavior, changing this setting will get around that, but means that ALL contacts with empty consent type will not be checked for CASL.{/ts}</p>
      <p class="description">{ts}If you choose "all", you need to be careful with new contacts so they don't get excluded right away. If you choose "skip empty", you need to be careful to get the consent type field filled in as much as possible.{/ts}</p>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.grant_consent.label}</div>
    <div class="content">{$form.grant_consent.html}
      <p class="description">{ts}Generally the CASL Support extension is meant to SET the no-bulk-email condition, and (usually) never to UNSET it. Here you can specify if it can be automatically unset (i.e. allow the contact to receive mailings again). This will happen for example when a contact changes from "no consent" to "explicit".{/ts}</p>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.autofill.label}</div>
    <div class="content">{$form.autofill.html}
      <p class="description">{ts}This allows you to specify the only conditions in which the CASL Support extension will AUTOMATICALLY populate the CASL fields for your contacts. In any other conditions you will have to manually update the CASL fields yourself, either using a manual process, a separate configuration (such as in a Drupal Webform), or custom PHP code.{/ts}</p>
    </div>
    <div class="clear"></div>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
