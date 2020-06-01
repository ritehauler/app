import GeoLib from "geolib";

import latLongPoints from "../sampleJson/latLongPoints.json";

class MapUtils {
  isPointInRegion(latitude, longitude) {
    return GeoLib.isPointInside(
      {
        latitude,
        longitude
      },
      latLongPoints
    );
  }
}

export default new MapUtils();
