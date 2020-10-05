import moment from "moment";
import {
  DATE_FORMAT,
  DISPLAY_DATE_FORMAT,
  GMT_TIME_FORMAT,
  ON_THE_WAY,
  DRIVER_ARRIVED,
  DRIVER_ACCEPTED,
  DRIVER_DECLINED,
  DRIVER_ASSIGNED,
  COMPLETED,
  DRIVER_REACHED,
  DRIVER_CANCELLED
} from "../../constant";
import Utils from "../../util";
import { Colors } from "../../theme";

const decideOrderStatus = status => {
  switch (status) {
    case DRIVER_ASSIGNED: {
      return { color: Colors.status.assigned, value: status };
    }
    case DRIVER_ACCEPTED: {
      return { color: Colors.status.accepted, value: status };
    }
    case DRIVER_ARRIVED: {
      return { color: Colors.status.arrived, value: status };
    }
    case ON_THE_WAY: {
      return { color: Colors.status.onTheWay, value: status };
    }
    case DRIVER_REACHED: {
      return { color: Colors.status.reached, value: status };
    }
    case COMPLETED: {
      return { color: Colors.status.completed, value: status };
    }
    case DRIVER_DECLINED: {
      return { color: Colors.status.declined, value: status };
    }
    case DRIVER_CANCELLED: {
      return { color: Colors.status.cancelled, value: status };
    }
    default: {
      return { color: "red", value: status };
    }
  }
};

export const OrderItemHandler = data => {
  return {
    orderEntityID: data.entity_id,
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
    status: decideOrderStatus(data.order_status.value)
  };
};
