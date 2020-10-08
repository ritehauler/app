import moment from "moment";
import {
  DATE_FORMAT,
  DISPLAY_DATE_FORMAT,
  GMT_TIME_FORMAT
} from "../../../constant";
import Utils from "../../../util";

export const OrderItemHandler = (data, userLocation) => {
  return {
    entityID: data.entity_id,
    time: Utils.formatDateLocal(
      `${data.pickup_date} ${data.pickup_time}`,
      GMT_TIME_FORMAT,
      DISPLAY_DATE_FORMAT
    ),
    location: data.order_pickup[0].address,
    payment: {
      id: data.order_number,
      method: data.payment_method_type.option,
      amount: data.pre_grand_total
    },
    distance: `${Utils.getDistanceInMiles(
      data.order_pickup[0].latitude,
      data.order_pickup[0].longitude,
      userLocation.latitude,
      userLocation.longitude
    )} mi`
  };
};
