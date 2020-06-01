// @flow
import _ from "lodash";
import { Actions } from "react-native-router-flux";
import {
  API_TRIP_NEW,
  API_TRIP_DECLINE,
  API_TRIP_ACCEPT,
  API_TRIP_PICK,
  API_TRIP_START,
  API_TRIP_DELIVER,
  API_TRIP_END,
  API_TRIP_ME,
  API_TRIP_STUCK
} from "../config/WebService";
import Util from "../util";
import { Images } from "../theme";
import { ORDER_STATUS, RIDE_MODE } from "../constant";
import { WarehousePresenter, AppDataPresenter, SettingPresenter } from "./";

class TripPresenter {
  tripRequest;

  init(tripRequest) {
    this.tripRequest = tripRequest;
  }

  sendNewTripRequest(appData: Object = {}) {
    const payload = {
      device_type: Util.getPlatform()
    };
    this.tripRequest(API_TRIP_NEW, payload, appData);
  }

  sendTripOrderRequest(
    Url: string,
    orderIds: any,
    location: Object,
    selectedOrders: any,
    orders: any,
    warehouseInfo: object = {},
    amount: number = 0
    
  ) {
    const payload = {
      request: orderIds,
      latitude: location.latitude,
      longitude: location.longitude,
      device_type: Util.getPlatform()
    };

    if (amount > 0) {
      payload.amount_received = amount;
    }

    this.tripRequest(Url, payload, {}, selectedOrders, orders, warehouseInfo);
  }

  sendDeclineRequest(tripId: any) {
    const payload = {
      trip_id: tripId,
      device_type: Util.getPlatform()
    };
    this.tripRequest(API_TRIP_DECLINE, payload);
  }

  sendMyTripOrdersRequest() {
    const payload = {
      device_type: Util.getPlatform()
    };
    this.tripRequest(API_TRIP_ME, payload);
  }

  sendAcceptRequest(orderIds: any, location: Object, warehouseInfo: Object, orders: any) {
    this.sendTripOrderRequest(API_TRIP_ACCEPT, orderIds, location, orders, orders, warehouseInfo);
  }

  sendPickRequest(orderIds: any, location: Object, orders: any) {
    this.sendTripOrderRequest(API_TRIP_PICK, orderIds, location, orders);
  }

  sendStartRequest(orderIds: any, location: Object, selectedOrders: any, orders: any) {
    this.sendTripOrderRequest(API_TRIP_START, orderIds, location, selectedOrders, orders);
  }

  sendDeliverRequest(orderIds: any, location: Object, orders: any) {
    this.sendTripOrderRequest(API_TRIP_DELIVER, orderIds, location, orders);
  }

  sendEndRequest(orderIds: any, location: Object, selectedOrders: any, orders: any, amount: number = 0) {
    this.sendTripOrderRequest(API_TRIP_END, orderIds, location, selectedOrders, orders, {}, amount);
  }

  sendStuckRequest(
    orderIds: any,
    selectedOrders: any,
    orders: any,
    location: Object,
    stuckReason: string,
    workerId: string = ""
  ) {

    const payload = {
      request: orderIds,
      latitude: location.latitude,
      longitude: location.longitude,
      stuck_reason: stuckReason,
      date: "2018-03-26",
      time: "14:37:00",
      radius: 20000,
      device_type: Util.getPlatform()
    };

    if (!Util.isEmpty(workerId)) {
      payload.worker_id = workerId;
    }

    this.tripRequest(API_TRIP_STUCK, payload, {}, selectedOrders, orders);
  }

  getLocationObject(latLong: any) {
    return {
      latitude: Util.toDouble(latLong[0]),
      longitude: Util.toDouble(latLong[1])
    };
  }

  getTripETAData(order: Object) {
    let tripData = {};
    const jsonTripData =
      (order.order_status === ORDER_STATUS.STARTED 
          || order.order_status === ORDER_STATUS.DELIVERED 
          || order.order_status === ORDER_STATUS.ENDED)
        ? order.eta_during_trip
        : (order.eta_after_stuck && !Util.isEmpty(order.eta_after_stuck))
          ? order.eta_after_stuck 
          : order.eta_before_trip;

    if (order && !Util.isEmpty(order) && !Util.isEmpty(jsonTripData)) {
      tripData = JSON.parse(jsonTripData);
    }

    return tripData;
  }

  getTripDistance(order: Object) {
    let distance = "";
    const tripData = this.getTripETAData(order);
    if (!Util.isEmpty(tripData)) {
      distance = this.isRideModeWalking(order) 
        ? tripData.info.walking.distance.text
        : tripData.info.driving.distance.text;
    }

    return distance;
  }

  getTripDuration(order: Object) {
    let duration = "";
    const tripData = this.getTripETAData(order);
    if (!Util.isEmpty(tripData)) {
      duration = this.isRideModeWalking(order) 
        ? tripData.info.walking.duration.text
        : tripData.info.driving.duration.text;
    }

    return duration;
  }

  getSourceLocation(order: Object) {
    const orderInfo = this.getTripETAData(order); // JSON.parse(order.eta_before_trip);
    const orderLatLong = this.isRideModeWalking(orderInfo) 
      ? orderInfo.info.walking.findings[0].origins.split(",")
      : orderInfo.info.driving.findings[0].origins.split(",") ;
    return this.getLocationObject(orderLatLong);
  }

  getDestinationLocation(order: Object) {
    const orderInfo = this.getTripETAData(order); // JSON.parse(order.eta_before_trip);
    const orderLatLong = this.isRideModeWalking(orderInfo) 
      ? orderInfo.info.walking.findings[0].destinations.split(",")
      : orderInfo.info.driving.findings[0].destinations.split(",");
    return this.getLocationObject(orderLatLong);
  }

  getDestinationLocationString(order: Object) {
    const orderInfo = this.getTripETAData(order); // JSON.parse(order.eta_before_trip);
    return this.isRideModeWalking(orderInfo) 
              ? orderInfo.info.walking.findings[0].destinations
              : orderInfo.info.driving.findings[0].destinations;
  }

  checkOrderbyStatus(
    order: any,
    orderStatus: string,
    defaultValue: boolean = false
  ) {
    const orderData = Array.isArray(order) ? order[0] : order;

    return orderData && !Util.isEmpty(orderData)
      ? orderData.order_status === orderStatus
      : defaultValue;
  }

  isOrderProcessing(order: any) {
    return this.checkOrderbyStatus(order, ORDER_STATUS.PROCESSING, true);
  }

  isOrderAssigned(order: any) {
    return this.checkOrderbyStatus(order, ORDER_STATUS.ASSIGNED);
  }

  isOrderPickUp(order: any) {
    return this.checkOrderbyStatus(order, ORDER_STATUS.PICK_UP);
  }

  isOrderPicked(order: any) {
    return this.checkOrderbyStatus(order, ORDER_STATUS.PICKED);
  }

  isOrderStarted(order: any) {
    return this.checkOrderbyStatus(order, ORDER_STATUS.STARTED);
  }

  isOrderDelivered(order: any) {
    return this.checkOrderbyStatus(order, ORDER_STATUS.DELIVERED);
  }

  isAnyOrderStarted(orders: any) {
    let isStarted = false;

    if (orders && !Util.isEmpty(orders) && Array.isArray(orders)) {

      const filterOrder = orders.filter(order => (order.order_status === ORDER_STATUS.STARTED 
        ||  order.order_status === ORDER_STATUS.DELIVERED 
        || order.order_status === ORDER_STATUS.ENDED));

      isStarted = (filterOrder && !Util.isEmpty(filterOrder));
    }

    return isStarted;
  }

  isContactDetailShow(order: any) {
    // const isOrderPicked = this.isOrderPicked(order);
    const isOrderStarted = this.isOrderStarted(order);
    const isOrderDelivered = this.isOrderDelivered(order);

    return isOrderStarted || isOrderDelivered;
  }

  updateAllSelectOrder(orderList: Object) {
    if (orderList && !Util.isEmpty(orderList)) {
      for (const order of orderList) {
        order.isSelected = order.isSelected ? false : true;
      }
    }

    return orderList;
  }

  updateSelectOrder(selectedOrder: Object, orderList: any) {
    if (selectedOrder && !Util.isEmpty(selectedOrder)) {
      for (const order of orderList) {
        if (selectedOrder.id === order.id) {
          order.isSelected = order.isSelected ? false : true;
          break;
        }
      }
    }

    return orderList;
  }

  updateSelectedOrders(selectedOrders: any, orderList: any) {
    let orders = orderList;
    if (selectedOrders && !Util.isEmpty(selectedOrders)) {
      for (const order of selectedOrders) {
        orders = this.updateSelectOrder(order, orders);
      }
    }

    return orders;
  }

  updateSelectOrders(selectedOrders: any, orderList: any) {
    let orders = orderList;
    if (orders && !Util.isEmpty(orders)) {
      const isArray = Array.isArray(selectedOrders);
      if (isArray) {
        orders = this.updateSelectedOrders(selectedOrders, orderList);
      } else {
        orders = this.updateSelectOrder(selectedOrders, orderList);
      }
    }

    return orders;
  }

  updateOrdersByIndex(orderList: any, index: any) {
    let orders = orderList;
    if (orderList && !Util.isEmpty(orderList)) {
      orders = _.cloneDeep(orderList);
      orders[index].isSelected = orders[index].isSelected ? false : true;
    }

    return orders;
  }

  removeOrders(orderList: any, selectedOrders: any) {
    let orders = orderList;
    if (orders && !Util.isEmpty(orders)) {
      orders = _.cloneDeep(orderList);
      const isArray = Array.isArray(orders);
      const isSelectedOrdersArray = Array.isArray(selectedOrders);

      if (isArray && isSelectedOrdersArray && orderList.length === selectedOrders.length) {
        orders = [];
      } else if (isArray && isSelectedOrdersArray) {
        orders = this.removeSelectedOrders(orderList, selectedOrders);
      } else {
        orders = this.removeOrder(orderList, selectedOrders);
      }
    }

    return orders;
  }

  removeSelectedOrders(orderList: any, selectedOrders: any) {
    let orders = orderList;
    if (selectedOrders && !Util.isEmpty(selectedOrders)) {
      for (const order of selectedOrders) {
        orders = this.removeOrder(orders, order);
      }
    }

    return orders;
  }

  removeOrder(orderList: any, selectedOrder: Object) {
    let orders = orderList;
    if (selectedOrder && !Util.isEmpty(selectedOrder)) {
      orders = orderList.filter(order => order.id !== selectedOrder.id);
    }

    return orders;
  }

  getOrderByStatus(orderList: any, orderStatus: string) {

    return (orderList && !Util.isEmpty(orderList)) 
      ? orderList.filter(order => (order.order_status === orderStatus))
      : [];
  }

  updateOrdersStatus(orderList: any, orderStatus: string, selectedOrders: any) {
    let orders = orderList;
    if (orders && !Util.isEmpty(orders)) {
      // orders = _.cloneDeep(orderList);
      const isArray = Array.isArray(orders);
      const isSelectedOrdersArray = Array.isArray(selectedOrders);

      if (isArray && isSelectedOrdersArray && orderList.length === selectedOrders.length) {
        orders = _.cloneDeep(selectedOrders);
        orders = this.updateAllOrdersStatus(orders, orderStatus);
      } else if (isArray && isSelectedOrdersArray) {
        orders = this.updateSelectedOrdersStatus(
          orderList,
          orderStatus,
          selectedOrders
        );
      } else {
        orders = this.updateOrderStatus(orderList, orderStatus, selectedOrders);
      }
    }

    return orders;
  }

  updateAllOrdersStatus(orderList: any, orderStatus: string) {
    for (const order of orderList) {
      order.order_status = orderStatus;
      order.isSelected = false;
    }

    return orderList;
  }

  updateSelectedOrdersStatus(
    orderList: any,
    orderStatus: string,
    selectedOrders: any
  ) {
    let orders = orderList;
    if (selectedOrders && !Util.isEmpty(selectedOrders)) {
      for (const order of selectedOrders) {
        orders = this.updateOrderStatus(orders, orderStatus, order);
      }
    }

    return orders;
  }

  updateOrderStatus(
    orderList: any,
    orderStatus: string,
    selectedOrder: Object
  ) {
    if (selectedOrder && !Util.isEmpty(selectedOrder)) {
      for (const order of orderList) {
        if (selectedOrder.id === order.id) {
          order.order_status = orderStatus;
          order.isSelected = false;
          order.eta_during_trip = selectedOrder.eta_during_trip;

          break;
        }
      }
    }

    return orderList;
  }

  getSelectedOrders(orderList: any) {
    let orders = [];
    if (orderList && !Util.isEmpty(orderList)) {
      orders = orderList.filter(order => order.isSelected);
    }

    return orders;
  }

  getOrderIds(orderList: any) {
    let orderIds = "";
    if (orderList && !Util.isEmpty(orderList)) {
      for (const order of orderList) {
        orderIds += order.id + ",";
      }
    }

    return orderIds.substring(0, orderIds.length - 1);
  }

  getSelectedOrderIds(orderList: any) {
    let orderIds = "";
    if (orderList && !Util.isEmpty(orderList)) {
      for (const order of orderList) {
        if (order.isSelected) {
          orderIds += order.id + ",";
        }
      }
    }

    return orderIds.substring(0, orderIds.length - 1);
  }

  getTripId(orderList: any) {
    let tripId = "";
    if (orderList && !Util.isEmpty(orderList)) {
      tripId = orderList[0].trip_id;
    }

    return tripId;
  }

  addDefaultValueInTripOrders(orderList: any) {
    if (orderList && !Util.isEmpty(orderList)) {
      for (const order of orderList) {
        order.manager = {
          name: "John Doe",
          image: Images.imagePic1,
          depotName: "Downtown Depot",
          locationName: "1242 Washington Blvd",
          amount: "$200",
          paymentType: "COD",
          distance: "2.5mi",
          date: "9 Sep, 8:05 AM"
        };
      }
    }

    return orderList;
  }

  getCurrentLocationMarker(currentLocation: Object) {
    const marker = {};
    marker.id = "1";
    marker.location = currentLocation;
    marker.isCurrentLocation = true;
    return marker;
  }

  getWareHouseLocationMarker(user: Object) {
    const warehouse = WarehousePresenter.getWarehouse(user);
    const location = WarehousePresenter.getWarehouseLocation(warehouse);
    const marker = {};
    if (!Util.isEmpty(location)) {
      marker.id = "2";
      marker.location = location;
      marker.isWarehouseLocation = true;
    }

    return marker;
  }

  getOrderMarker(orders: any) {
    const marker = {};
    const location = this.getDestinationLocation(orders[0]);
    marker.id = orders[0].id;
    marker.location = location;
    marker.orders = orders;

    return marker;
  }

  getOrderMapFromArray(orderList: any) {
    const map = new Map();
    for (const order of orderList) {
      const key = this.getDestinationLocationString(order);
      if (map.has(key)) {
        const orders = map.get(key);
        orders.push(order);
      } else {
        const orders = new Array();
        orders.push(order);
        map.set(key, orders);
      }
    }

    return map;
  }

  getMarkerCoordinates(markers: any) {
    const markerCoordinates = [];
    if (markers && !Util.isEmpty(markers)) {
      for (const marker of markers) {
        markerCoordinates.push(marker.location);
      }
    }

    return markerCoordinates;
  }

  getMarkers(currentLocation: Object, user: Object, orderList: any) {
    const markers = [];
    const warehouseMarker = this.getWareHouseLocationMarker(user);

    markers.push(this.getCurrentLocationMarker(currentLocation));
    if (!Util.isEmpty(warehouseMarker)) {
      markers.push(warehouseMarker);
    } 

    if (orderList && !Util.isEmpty(orderList)) {
      const orderMap = this.getOrderMapFromArray(orderList);
      for (const orderKey of orderMap.keys()) {
        markers.push(this.getOrderMarker(orderMap.get(orderKey)));
      }
    }

    return markers;
  }

  getOrderDetailItems(order: Object) {
    let orderDetail = {};
    if (order && !Util.isEmpty(order) && !Util.isEmpty(order.metadata)) {
      orderDetail = JSON.parse(order.metadata);
    }

    return orderDetail;
  }

  getTitleByOrderStatusBeforePicked(orderList: any) {
    let title = "";

    if (!Util.isEmpty(orderList)) {
      const isOrderProcessing = this.isOrderProcessing(orderList);
      const isOrderAssigned = this.isOrderAssigned(orderList);
      const isOrderPickUp = this.isOrderPickUp(orderList);

      if (isOrderProcessing) {
        title = "ACCEPT";
      } else if (isOrderAssigned) {
        title = "REACHED";
      } else if (isOrderPickUp) {
        title = "PICKED";
      }
    }

    return title;
  }

  getTitleByOrderStatusAfterPicked(orderList: any) {
    let title = "";

    if (!Util.isEmpty(orderList)) {
      const isOrderPicked = this.isOrderPicked(orderList);
      const isOrderStarted = this.isOrderStarted(orderList);
      // const isOrderDelivered = this.isOrderDelivered(orderList);

      if (isOrderPicked) {
        title = "ON THE WAY";
      } else if (isOrderStarted) {
        title = "DELIVERED";
      }
    }

    return title;
  }

  getOrderStatusByUrl(url: string) {
    let orderStatus = "";
    switch (url) {
      case API_TRIP_ACCEPT:
        orderStatus = ORDER_STATUS.ASSIGNED;
        break;

      case API_TRIP_PICK:
        orderStatus = ORDER_STATUS.PICKED;
        break;

      case API_TRIP_START:
        orderStatus = ORDER_STATUS.STARTED;
        break;

      case API_TRIP_DELIVER:
        orderStatus = ORDER_STATUS.DELIVERED;
        break;

      case API_TRIP_END:
        orderStatus = ORDER_STATUS.ENDED;
        break;

      default:
        orderStatus = "";
        break;
    }

    return orderStatus;
  }

  isRideModeWalking(order: Object) {
    return order.ride_mode === RIDE_MODE.WALKING;
  }

  getCustomerDetail(order: Object) {
    const customerInfo = JSON.parse(order.consignment);
    return this.isRideModeWalking(order) ? customerInfo.walking : customerInfo.driving;
  }

  getCustomerName(customerInfo: Object) {
    let name = "";
    if (customerInfo && !Util.isEmpty(customerInfo)) {
      const { first_name, last_name } = customerInfo;
      name = first_name + " " + last_name;
    }

    return name;
  }

  getCustomerHashId(customerInfo: Object) {
    let hashId = "";

    if (
      customerInfo &&
      !Util.isEmpty(customerInfo) &&
      customerInfo.documents &&
      !Util.isEmpty(customerInfo.documents)
    ) {
      hashId = customerInfo.documents[0].hash_id;
    }

    return hashId;
  }

  getCustomerImageSource(customerInfo: Object, defaultImage: Object) {
    const hashId = this.getCustomerHashId(customerInfo);
    return Util.getImageSource(hashId, defaultImage);
  }

  updateTripData(tripData: Object, selectedOrders: any) {
    let orders = selectedOrders;
    if (
      selectedOrders &&
      !Util.isEmpty(selectedOrders) &&
      tripData &&
      !Util.isEmpty(tripData)
    ) {
      orders = _.cloneDeep(selectedOrders);
      for (const key of Object.keys(tripData)) {
        for (const order of orders) {
          if (order.id === key) {
            order.eta_during_trip = tripData[key];
          }
        }
      }
    }

    return orders;
  }

  isAllOrdersSelected(orderList: any) {
    let selected = true;

    for (const order of orderList) {
      if (!order.isSelected) {
        selected = false;
        break;
      }
    }

    return selected;
  }

  getOrdersTotalAmount(orderList: any) {

    let totalAmount = 0;
    if (orderList && !Util.isEmpty(orderList)) {
      for (const order of orderList) {
        totalAmount += Util.toDouble(order.order_cost);
      }
    }

    return totalAmount;
  }

  isCombinedMode(order: Object) {
    const orderData = Array.isArray(order) ? order[0] : order;
    return orderData.ride_mode === RIDE_MODE.COMBINED;
  }

  getCustomerNamePrefix(customerInfo: Object) {
    let title = "TT";
    if (customerInfo && !Util.isEmpty(customerInfo)) {
      const { first_name, last_name } = customerInfo;
      title = first_name.charAt(0) + last_name.charAt(0);
    }

    return title;
  }

  getOrderTabIndex(orders: any) {
    const isOrderAssigned = this.isOrderAssigned(orders);
    const isOrderPickUp = this.isOrderPickUp(orders);

    return (isOrderAssigned || isOrderPickUp) ? 1 : 0;
  }

  handleOrderPayment(orders: any, selectedOrders: any, isOrderDelivered: boolean = true) {
    const isCombinedMode = this.isCombinedMode(selectedOrders);

    if (isCombinedMode) {
      Actions.orderBundleReceipt({
        order: selectedOrders[0], data: selectedOrders, assignedOrders: orders, isReceive: isOrderDelivered
      });
    } else {
      const orderDetail = this.getOrderDetailItems(selectedOrders[0]);
      Actions.orderReceipt({ order: selectedOrders[0], assignedOrders: orders, isReceive: isOrderDelivered, orderDetail });
    }
  }

  isTripAcceptTimePassed(tripData: Object, appData: Object, settingData: object) {
    let isTripTimePassed = false;
    if (tripData.trips && !Util.isEmpty(tripData.trips)) {
      const orderAcceptTime = SettingPresenter.getOrderDispatchDelay(settingData);
      const orderAcceptStartTime =  AppDataPresenter.getOrderAcceptStartTime(appData);
      const passedOrderAcceptTime = Util.getTimeDifferenceInSecs(orderAcceptStartTime, Util.getCurrentDateTime());
      isTripTimePassed = (passedOrderAcceptTime > orderAcceptTime);
    }

    return isTripTimePassed;
  }

  isOrders(orders: any) {

    return orders && !Util.isEmpty(orders);
  }
}

export default new TripPresenter();
