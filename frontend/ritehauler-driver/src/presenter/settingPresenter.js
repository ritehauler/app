// @flow
import { API_SETTINGS } from "../config/WebService";
import Util from "../util";

const settingList = [
  {
    key: "order_dispatch_delay",
    value: "60",
    description:
      "The time to make delay to dispatch or re-dispatch orders to delivery workers.",
    value_unit: "seconds"
  },
  {
    key: "latlng_update_range",
    value: "20",
    description: "The difference of two distances required to update latlng.",
    value_unit: "meters"
  },
  {
    key: "latlng_update_time",
    value: "10",
    description: "Send latlng data on intervals",
    value_unit: "seconds"
  },
  {
    key: "reached_proximity",
    value: "1000",
    description:
      "The proximity distance to be covered to reach the area/place.",
    value_unit: "meters"
  },
  {
    key: "new_trip_request_delay",
    value: "10",
    description:
      "send new trip request on specific intervals.",
    value_unit: "seconds"
  },
  {
    key: "currency_code",
    value: "₦",
    description:
      "default currency code.",
    value_unit: "currency"
  },
  {
    key: "customer_service_num",
    value: "+92-3453823246",
    description:
      "Customer service number.",
    value_unit: "number"
  }, 
  {
    key: "stats_record_limit",
    value: "3",
    description:
      "Show previous performance stat based on month limit.",
    value_unit: "number"
  }
];

class SettingPresenter {
  settingRequest;

  init(settingRequest) {
    this.settingRequest = settingRequest;
  }

  sendSettingRequest() {
    const payload = {
      device_type: Util.getPlatform()
    };

    this.settingRequest(API_SETTINGS, payload);
  }

  getDataByKey(data: Object, key: string) {
    return data &&
      !Util.isEmpty(data) &&
      data.listing &&
      !Util.isEmpty(data.listing)
      ? data.listing.find(item => item.key === key)
      : settingList.find(item => item.key === key);
  }

  getOrderDispatchDelay(data: Object) {
    const key = "order_dispatch_delay";
    const orderDispatchDelay = this.getDataByKey(data, key);
    const value = Util.getTimeInSeconds(
      orderDispatchDelay.value,
      orderDispatchDelay.value_unit
    );

    return value;
  }

  getLatLngUpdateRange(data: Object) {
    const key = "latlng_update_range";
    return this.getDataByKey(data, key);
  }

  getLatLngUpdateTime(data: Object) {
    const key = "latlng_update_time";
    const latLngUpdateTime = this.getDataByKey(data, key);
    const value = Util.getTimeInSeconds(
      latLngUpdateTime.value,
      latLngUpdateTime.value_unit
    );

    return value;
  }

  getReachedProximity(data: Object) {
    const key = "reached_proximity";
    return this.getDataByKey(data, key);
  }

  getCurrency(data: Object) {
    const key = "currency_code";
    return this.getDataByKey(data, key).value;
  }

  getNewTripRequestDelay(data: Object) {
    const key = "new_trip_request_delay";
    const newTripRequestDelay = this.getDataByKey(data, key);
    const value = Util.getTimeInSeconds(
      newTripRequestDelay.value,
      newTripRequestDelay.value_unit
    );

    return value;
  }

  getCustomerServiceNumber(data: Object) {
    const key = "customer_service_num";
    return this.getDataByKey(data, key).value;
  }

  getStatsRecordLimit(data: Object) {
    const key = "stats_record_limit";
    const statsLimit = this.getDataByKey(data, key).value;
    return Util.toInt(statsLimit);
  }
}

export default new SettingPresenter();
