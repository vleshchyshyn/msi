/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/components/fieldset'
], function (Fieldset) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            canManageStock: true,
            additionalClasses: {},
            imports: {
                onStockChange: '${ $.provider }:data.product.stock_data.manage_stock'
            }
        },

        onStockChange: function(manageStockValue) {
            if (manageStockValue === 0) {
                // this.canManageStock = false;
                this.delegate('disabled', true);
            }
        }

    });
});
