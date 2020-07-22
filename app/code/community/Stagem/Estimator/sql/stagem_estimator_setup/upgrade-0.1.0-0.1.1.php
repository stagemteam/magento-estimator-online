<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
#Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$content = <<<HTML
<div class="powerInfo" style="display: none">
    <table>
        <tbody>
        <tr>
            <th>{{translate text="Area To Be Cooled (square feet)"}}</th>
            <th>{{translate text="Capacity Needed (BTUs per hour)"}}</th>
        </tr>
        <tr>
            <td>450 {{translate text="up to"}} 550</td>
            <td>12,000</td>
        </tr>
        <tr>
            <td>550 {{translate text="up to"}} 700</td>
            <td>14,000</td>
        </tr>
        <tr>
            <td>700 {{translate text="up to"}} 1,000</td>
            <td>18,000</td>
        </tr>
        <tr>
            <td>1,000 {{translate text="up to"}} 1,200</td>
            <td>21,000</td>
        </tr>
        <tr>
            <td>1,200 {{translate text="up to"}} 1,400</td>
            <td>23,000</td>
        </tr>
        </tbody>
    </table>
</div>
HTML;

$staticBlock = [
    'title' => 'BTU-Capacity comparison table',
    'identifier' => 'btu_capacity_comparison_table',
    'content' => $content,
    'is_active' => 1,
    'stores' => [0],
];

Mage::getModel('cms/block')->setData($staticBlock)->save();

$installer->endSetup();
