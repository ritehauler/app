import { Strings } from "../theme";
import Utils from "../util";

export default {
  getTitle(data) {
    return data.title || "";
  },

  getEntityId(data) {
    return data.entity_id;
  },

  getTabImage(data, index) {
    return Utils.getImageFromGallery(data[index].gallery);
  },

  getImage(data) {
    return Utils.getImageFromGallery(data.gallery, 1);
  },

  getWeightInfo(data) {
    const { truck_class_id } = data;
    const weightInKg =
      truck_class_id &&
      truck_class_id.detail &&
      truck_class_id.detail.weight_kg &&
      truck_class_id.detail.weight_kg.min_weight
        ? `${Utils.fixToDecimal(
            truck_class_id.detail.weight_kg.min_weight
          )}-${Utils.fixToDecimal(truck_class_id.detail.weight_kg.max_weight)}`
        : "0";
    const weightInPonds =
      truck_class_id &&
      truck_class_id.detail &&
      truck_class_id.detail.min_weight
        ? `${Utils.fixToDecimal(
            truck_class_id.detail.min_weight
          )}-${Utils.fixToDecimal(truck_class_id.detail.max_weight)}`
        : "0";
    return `${weightInPonds} ${Strings.unitNameWeight} (${weightInKg} ${
      Strings.unitNameKg
    })`;
  },

  getBaseFee(data) {
    const baseFee = data.base_fee || 0;
    return Utils.getFormattedPrice(baseFee);
  },

  getChargePerMin(data) {
    const chargePerMinute = data.charge_per_minute || "0";
    return Utils.getFormattedPrice(chargePerMinute);
  },

  getEstimatedCost(data) {
    const minimumCharges = data.est_min_charges || "0";
    const maximumCharges = data.est_max_charges || "0";
    if (minimumCharges === maximumCharges) {
      return Utils.getFormattedPrice(maximumCharges);
    }
    return `${Utils.getFormattedPrice(
      minimumCharges
    )}-${Utils.getFormattedPrice(maximumCharges)}`;
  },
  getCost(data) {
    return data.est_max_charges || data.est_min_charges || 0;
  }
};
