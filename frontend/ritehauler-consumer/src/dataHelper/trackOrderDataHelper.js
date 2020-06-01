import _ from "lodash";

import Utils from "../util";

export default {
  getPickUpLatLong(data) {
    const { order_pickup } = data;
    if (_.isEmpty(order_pickup, true)) {
      return undefined;
    }
    return {
      latitude: Number(order_pickup[0].latitude),
      longitude: Number(order_pickup[0].longitude)
    };
  },
  getDropOffLatLong(data) {
    const { order_dropoff } = data;
    if (_.isEmpty(order_dropoff, true)) {
      return undefined;
    }
    return {
      latitude: Number(order_dropoff[0].latitude),
      longitude: Number(order_dropoff[0].longitude)
    };
  },
  getCoordinates(data) {
    const { driver_location } = data;
    if (_.isEmpty(driver_location, true)) {
      return [];
    }
    return driver_location;
  }
};
