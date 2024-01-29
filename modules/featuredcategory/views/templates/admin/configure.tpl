{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Featured Category' mod='featuredcategory'}</h3>
	<p>
		<strong>{l s='Here is my new generic module!' mod='featuredcategory'}</strong><br />
		{l s='This is no editable from back office.' mod='featuredcategory'}<br />
		{l s='It is based on three categories most visited by user in front.' mod='featuredcategory'}<br />
		{l s='This module use hook "actionProductCategory" and create column "visit" in table ps_category.' mod='featuredcategory'}
	</p>
	<br />
	<p>
		{l s='This module will boost your sales!' mod='featuredcategory'}
	</p>
</div>

<div class="panel">
	<table class="table">
		{foreach from=$datas item=categ}
			<tr>
				<td>{$categ['id_category']}</td>
				<td>{$categ['name']}</td>
			</tr>
		{/foreach}
	</table>
</div>
