// @flow
import { API_ORDER_HISTORY, API_ORDER_RANGE } from "../config/WebService";
import { ORDER_HISTORY_STATUS, PAGE_SIZE } from "../constant";
import Util from "../util";

class OrderHistoryPresenter {
  orderHistoryRequest;

  init(orderHistoryRequest) {
    this.orderHistoryRequest = orderHistoryRequest;
  }

  sendOrderHistoryRequest(orderFilter: Object = {}, currentPage: number = 1)  {
    const payload = {
      device_type: Util.getPlatform(),
      page: currentPage,
      page_size: PAGE_SIZE,
      search: this.getOrderFilterArray(orderFilter)
    };

    this.orderHistoryRequest(API_ORDER_HISTORY, payload);
  }

  sendOrderRangeRequest(orderRangeRequest) {
    const payload = {
      device_type: Util.getPlatform(),
    };

    orderRangeRequest(API_ORDER_RANGE, payload);
  }

  getSectionOrder(orders: any) {
    return {
        title:  Util.orderDateFormat(orders[0].delivery_date + " " + orders[0].delivery_time),
        data: orders
    };
  }
  
  getSectionOrderList(orders: any) {

    const sectionOrderList = [];
    if (orders && !Util.isEmpty(orders)) {

        let deliveryDate = orders[0].delivery_date;
        let sectionOrders = []
        let i = 0;
        for ( const order of orders) {

            if (order.delivery_date !== deliveryDate) {
                sectionOrderList.push(this.getSectionOrder(sectionOrders));
                sectionOrders = [];
                deliveryDate = order.delivery_date;
            }

            sectionOrders.push(order);
            i++;

            if (i === orders.length) {
                sectionOrderList.push(this.getSectionOrder(sectionOrders));
                sectionOrders = [];
            }
        }
    }

    return sectionOrderList;
  }

  getOldOrderDate(orderData: Object) {
    let date = Util.getCurrentDate();

    if (orderData.trips && !Util.isEmpty(orderData.trips)) {
        const order = orderData.trips[orderData.trips.length - 1];
        date = order.delivery_date;
    }

    return date;
  }

  getOrderFilterArray(orderFilter: Object) {
    const filterData = [];

    filterData.push("delivery_status,=," + ORDER_HISTORY_STATUS.COMPLETED);
    if (orderFilter && !Util.isEmpty(orderFilter)) {
      if (orderFilter.paymentType && !Util.isEmpty(orderFilter.paymentType)) {
        filterData.push("payment_method,=," + orderFilter.paymentType);
      }

      filterData.push("order_cost,>=," + orderFilter.priceMinValue);
      filterData.push("order_cost,<=," + orderFilter.priceMaxValue);
      filterData.push("delivery_date,>=," + orderFilter.startDate);
      filterData.push("delivery_date,<=," + orderFilter.endDate);
    }

    return filterData;
  }

  getOrderNearestValue(data: any) {

    const value = Util.toInt(data);
    return Util.getNearestIntValue(value);

  }

  getOrderRangeValue(data: Object) {

    return (data && !Util.isEmpty(data) && data.maxmin)
      ? data.maxmin 
      : {}
  }

  getOrderMaxValue(orderPriceRange: Object, priceMaxValue: number) {

    return (!Util.isEmpty(orderPriceRange) && orderPriceRange.max && !Util.isEmpty(orderPriceRange.max)) ? this.getOrderNearestValue(orderPriceRange.max) : priceMaxValue;
  }

  getOrderMinValue(orderPriceRange: Object, priceMinValue: number) {

    return (!Util.isEmpty(orderPriceRange) && orderPriceRange.min && !Util.isEmpty(orderPriceRange.min)) ? orderPriceRange.min : priceMinValue;
  }
}

export default new OrderHistoryPresenter();
