import {
  ENTITY_TYPE_ID_ORDER,
  ENTITY_TYPE_ID_ORDER_ITEM,
  ORDER_STATUS_PENDING
} from "../config/WebService";

import Utils from "../util";
import vehicleDataHelper from "./vehicleDataHelper";

export default {
  getPayLoadOrderPlace(orderInfo, user) {
    // get items and info object from orderInfo
    const { items, info } = orderInfo;

    // set item array
    const depend_entity = [];
    for (let i = 0; i < items.length; i += 1) {
      // get data of single item
      const item = items[i];
      const receiptImages = item.receiptImages || [];
      const itemImages = item.itemImages || [];

      // set attachment id's of images to send
      const itemImagesArray = [];
      itemImages.map(itemImage =>
        itemImagesArray.push(itemImage.attachment.attachment_id)
      );
      const itemImagesIds =
        itemImagesArray.length > 0 ? itemImagesArray.join() : "";

      // set attachment id's of receipt images to send
      const receiptImagesArray = [];
      receiptImages.map(receiptImage =>
        receiptImagesArray.push(receiptImage.attachment.attachment_id)
      );
      const receiptImagesIds =
        receiptImagesArray.length > 0 ? receiptImagesArray.join() : "";

      // set name and id of item
      const itemName = item.otherItemText || item.itemName.title;
      const itemId =
        item.itemName && item.itemName.entity_id ? item.itemName.entity_id : "";

      // set is expensive
      const is_expensive = item.showReceipt ? 1 : 0;

      // set item object
      const dependItem = {
        item_name: itemName,
        item_id: itemId,
        quantity: item.quantity,
        price: item.price,
        entity_type_id: ENTITY_TYPE_ID_ORDER_ITEM,
        width: item.width,
        height: item.height,
        length: item.length,
        volume: item.itemBox.volume,
        weight: item.weight,
        is_expensive,
        item_box_id: item.itemBox.entity_id,
        item_box_title: item.itemBox.title,
        item_images: itemImagesIds,
        item_receipt: receiptImagesIds
      };

      // add item in array
      depend_entity.push(dependItem);
    }

    // set payload of order place
    const orderPayload = {
      entity_type_id: ENTITY_TYPE_ID_ORDER,
      order_status: ORDER_STATUS_PENDING,
      customer_id: user.entity_id,
      truck_selected_id: info.truck_selected_id,
      truck_id: info.truck_id,
      professional_id: info.professional_id,
      pickup_date: info.pickup_date,
      pickup_time: info.pickup_time,
      weight: info.weight,
      card_id: info.card_id,
      card_type: info.card_type,
      card_last_digit: info.card_last_digit,
      volume: info.volume,
      order_notes: info.order_notes,
      pickup: info.pickup,
      dropoff: info.dropoff,
      depend_entity,
      hook: "order_item,order_pickup,order_dropoff",
      mobile_json: 1
    };
    /*
    pickup_time_gmt: info.pickup_time_gmt,
    pickup_date_gmt: info.pickup_date_gmt,
    */
    return orderPayload;
  },
  getPayLoadSaveVehicleForOrder(orderInfo) {
    const { info, items } = orderInfo;
    // calculate weight and volume
    let weight = 0;
    let volume = 0;
    for (let i = 0; i < items.length; i += 1) {
      weight += Number(items[i].weight) * Number(items[i].quantity);
      volume += Number(items[i].itemBox.volume) * Number(items[i].quantity);
    }

    // set payload
    const payloadSelectVehicle = {
      weight,
      volume,
      pickup_latitude: info.pickup.latitude,
      pickup_longitude: info.pickup.longitude,
      dropoff_latitude: info.dropoff.latitude,
      dropoff_longitude: info.dropoff.longitude
    };

    // send payload
    return payloadSelectVehicle;
  },
  getTotalCost(orderInfo) {
    const { deliveryProfessional, vehicle } = orderInfo;
    const loadingPrice = deliveryProfessional.price || 0;
    const vehicleCost = vehicleDataHelper.getCost(vehicle);
    const totalCost = Number(loadingPrice) + Number(vehicleCost);
    return Utils.getFormattedPrice(totalCost);
  }
};
