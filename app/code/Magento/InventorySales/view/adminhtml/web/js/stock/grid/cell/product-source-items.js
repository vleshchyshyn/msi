/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'underscore'
], function (Column, _) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magento_InventorySales/stock/grid/cell/product-source-items.html'
        },

        /**
         * Get product source items
         *
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getProductSourceItems: function (record) {
            var result = [];

            _.each(record[this.index], function (sourceItem) {
                result.push({
                    name: sourceItem.name,
                    qty: sourceItem.qty
                });
            });

            return result;
        }
    });
});
