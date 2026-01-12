{**
 * 2007-2025 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-bank"></i> {l s='Wire Payment Multi - Bank Accounts Management' mod='pswirepaymentmulti'}
    </div>
    <div class="panel-body">
        <div class="alert alert-info">
            <p><strong>{l s='Multiple Bank Accounts' mod='pswirepaymentmulti'}</strong></p>
            <p>{l s='This module allows you to display multiple bank accounts to your customers during checkout.' mod='pswirepaymentmulti'}</p>
            <p>{l s='You currently have %d bank account(s) configured.' sprintf=[$accounts_count] mod='pswirepaymentmulti'}</p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="{$link_manage|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-lg">
                    <i class="icon-cog"></i> {l s='Manage Bank Accounts' mod='pswirepaymentmulti'}
                </a>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h4><i class="icon-info-circle"></i> {l s='How it works' mod='pswirepaymentmulti'}</h4>
                <ul>
                    <li>{l s='Add as many bank accounts as you need' mod='pswirepaymentmulti'}</li>
                    <li>{l s='Upload a logo for each bank (recommended: 200x100px)' mod='pswirepaymentmulti'}</li>
                    <li>{l s='Active accounts will be displayed to customers during checkout' mod='pswirepaymentmulti'}</li>
                    <li>{l s='You can reorder accounts by dragging them in the list' mod='pswirepaymentmulti'}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h4><i class="icon-check"></i> {l s='Features' mod='pswirepaymentmulti'}</h4>
                <ul>
                    <li>{l s='Unlimited bank accounts' mod='pswirepaymentmulti'}</li>
                    <li>{l s='Custom bank logos' mod='pswirepaymentmulti'}</li>
                    <li>{l s='Enable/disable accounts individually' mod='pswirepaymentmulti'}</li>
                    <li>{l s='Compatible with PrestaShop 1.7, 8 and 9' mod='pswirepaymentmulti'}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
