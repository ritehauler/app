import { DATE_TIME_FORMAT, DISPLAY_DATE_TIME_FORMAT_ORDER } from "../constants";
import {
  ORDER_STATUS_PENDING,
  ORDER_STATUS_ON_THE_WAY,
  ORDER_STATUS_CONFIRMED,
  ORDER_STATUS_CANCELLED,
  ORDER_STATUS_DRIVER_CANCELLED,
  ORDER_STATUS_PAYMENT_REQUIRED,
  ORDER_STATUS_DRIVER_ARRIVED,
  ORDER_STATUS_COMPLETE
} from "../config/WebService";
import Util from "../util";

export default {
  getOrderStatusColor(data) {
    const { order_status } = data;
    const orderStatus =
      order_status && order_status.detail && order_status.detail.display_key
        ? order_status.detail.display_key
        : ORDER_STATUS_PENDING;
    let color;
    switch (orderStatus) {
      case ORDER_STATUS_CANCELLED:
      case ORDER_STATUS_DRIVER_CANCELLED:
        color = "error";
        break;
      case ORDER_STATUS_COMPLETE:
      case ORDER_STATUS_CONFIRMED:
      case ORDER_STATUS_ON_THE_WAY:
      case ORDER_STATUS_DRIVER_ARRIVED:
        color = "success";
        break;
      case ORDER_STATUS_PENDING:
        color = "pending";
        break;
      case ORDER_STATUS_PAYMENT_REQUIRED:
        color = "info";
        break;
      default:
        color = "pending";
    }
    return color;
  },
  getOrderStatusTitle(data) {
    const { order_status } = data;
    const orderStatus =
      order_status && order_status.detail && order_status.detail.display_title
        ? order_status.detail.display_title
        : ORDER_STATUS_PENDING;
    return orderStatus;
  },
  driverCancelRequiredPayment(data) {
    const { order_status, transaction_id } = data;
    const orderStatus =
      order_status && order_status.detail && order_status.detail.display_key
        ? order_status.detail.display_key
        : ORDER_STATUS_PENDING;

    return (
      orderStatus === ORDER_STATUS_DRIVER_CANCELLED && transaction_id === ""
    );
  },
  getOrderStatus(data) {
    const { order_status } = data;
    const orderStatus =
      order_status && order_status.detail && order_status.detail.display_key
        ? order_status.detail.display_key
        : ORDER_STATUS_PENDING;
    return orderStatus;
  },
  getOrderId(data) {
    const { order_number, entity_id } = data;
    return order_number || `RH${entity_id}`;
  },
  getFormatDateAndTime(
    pickup_date,
    pickup_time,
    format = DISPLAY_DATE_TIME_FORMAT_ORDER
  ) {
    const pickUpDateTime = `${pickup_date} ${pickup_time}`;
    const displayDateTimeOrder = Util.formatDateLocal(
      pickUpDateTime,
      DATE_TIME_FORMAT,
      format
    );
    return displayDateTimeOrder;

    //const { pickup_date, pickup_time } = data;
    /*
    const formatDate = Util.formatDate(
      pickup_date,
      DATE_FORMAT,
      DISPLAY_DATE_ORDER_FORMAT
    );
    const formatTime = Util.formatDate(
      pickup_time,
      TIME_FORMAT,
      DISPLAY_TIME_FORMAT
    );
    return `${formatDate}, ${formatTime}`;
      */
  },
  getPickUpAddress(data) {
    const { order_pickup } = data;
    const address =
      order_pickup && order_pickup.length > 0 ? order_pickup[0].address : "";
    return address;
  },
  getDropOffAddress(data) {
    const { order_dropoff } = data;
    const address =
      order_dropoff && order_dropoff.length > 0 ? order_dropoff[0].address : "";
    return address;
  },
  getOrderAmount(data) {
    const { pre_grand_total, grand_total } = data;
    const amount = grand_total || pre_grand_total || "0";
    return Util.getFormattedPrice(amount);
  },
  getOrderCardName(data) {
    return data.card_type || "";
  },
  getOrderDistance(data) {
    const { estimated_distance, total_distance } = data;
    const distance = total_distance || estimated_distance || "0";
    return `${distance} mi`;
  },
  getOrderDuration(data) {
    const { estimated_minutes, total_minutes } = data;
    const distance = total_minutes || estimated_minutes || "0";
    return `${distance} mins`;
  },
  getOrderVehicleName(data) {
    const { truck_id, vehicle_id } = data;
    const truckName = truck_id.value || "";
    const vehicleName =
      vehicle_id && vehicle_id.value ? `(${vehicle_id.value})` : "";
    return `${truckName}${vehicleName}`;
  },
  getOrderVehicleCode(data) {
    const { vehicle_id } = data;
    const vehicleCode =
      vehicle_id.detail && vehicle_id.detail.vehicle_code
        ? vehicle_id.detail.vehicle_code
        : "";
    return vehicleCode;
  },
  getDriverName(data) {
    const { driver_id } = data;
    const driverName = driver_id && driver_id.value ? driver_id.value : "";
    return driverName;
  },
  getDriverId(data) {
    const { driver_id } = data;
    const driverId = driver_id && driver_id.id ? driver_id.id : 0;
    return driverId;
  },
  getBaseFee(data) {
    const baseFee = data.base_fee || 0;
    return Util.getFormattedPrice(baseFee);
  },
  getPerMinutePrice(data) {
    const chargePerMinute = data.charge_per_minute || 0;
    return Util.getFormattedPrice(chargePerMinute);
  },
  getDeliveryProfessional(data) {
    return data.number_of_labour || "";
  },
  getLoadingPrice(data) {
    const loadingPrice = data.loading_price || 0;
    return Util.getFormattedPrice(loadingPrice);
  },
  getOrderItems(data) {
    const { order_item } = data;
    const itemsList = [];
    const extraItemList = [];
    if (order_item && order_item.length > 0) {
      for (let i = 0; i < order_item.length; i += 1) {
        const orderItem = order_item[i];
        const showReceipt =
          orderItem.is_expensive &&
          orderItem.is_expensive.value &&
          orderItem.is_expensive.value === "1";

        const item = {
          width: orderItem.width || "0",
          height: orderItem.height || "0",
          length: orderItem.length || "0",
          weight: orderItem.weight || "0",
          quantity: orderItem.quantity || "0",
          volume: orderItem.volume || "0",
          itemBox: orderItem.item_box_id || {},
          per_extra_item_charge: orderItem.per_extra_item_charge || "",
          otherItemText: orderItem.item_name,
          showReceipt
        };
        const isExtraItem =
          orderItem.is_extra_item &&
          orderItem.is_extra_item.value &&
          orderItem.is_extra_item.value === "1";
        if (isExtraItem) {
          extraItemList.push(item);
        } else {
          itemsList.push(item);
        }
      }
    }
    return { itemsList, extraItemList };
  },
  getEstimatedCost(data) {
    const minEstimatedCharges = data.min_estimated_charges || "0";
    const maxEstimatedCharges = data.max_estimated_charges || "0";
    const actualCharges = data.actual_charges || "";

    if (actualCharges !== "") {
      return Util.getFormattedPrice(actualCharges);
    } else if (minEstimatedCharges === maxEstimatedCharges) {
      return Util.getFormattedPrice(maxEstimatedCharges);
    }
    return `${Util.getFormattedPrice(
      minEstimatedCharges
    )}-${Util.getFormattedPrice(maxEstimatedCharges)}`;
  }
};
