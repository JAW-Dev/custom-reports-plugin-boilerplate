// Import Modules
import report from './modules/report';

report({
	filename: 'orders-report',
	target: 'orders-report',
	action: 'orders_report'
});

report({
	filename: 'product-images-report',
	target: 'product-images-report',
	action: 'product_images_report'
});

report({
	filename: 'listrak-orders-report',
	target: 'listrak-orders-report',
	action: 'listrak_orders_report'
});

report({
	filename: 'listrak-order-items-report',
	target: 'listrak-order-items-report',
	action: 'listrak_order_items_report'
});

report({
	filename: 'listrak-customers-report',
	target: 'listrak-customers-report',
	action: 'listrak_customes_report'
});

report({
	filename: 'listrak-products-report',
	target: 'listrak-products-report',
	action: 'listrak_products_report'
});
