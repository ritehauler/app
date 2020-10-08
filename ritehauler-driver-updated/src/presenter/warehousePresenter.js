
// @flow
import Util from "../util";
import { RIDE_MODE } from "../constant";
import { MapRoutePresenter } from "./";

class WarehousePresenter {

    getWarehouseById(warehouseId: string, warehouses: any) {
        let warehouseObject = {};
        for (const warehouse of warehouses) {
          if (warehouse.id === warehouseId) {
            warehouseObject = warehouse;
          }
        }
    
        return warehouseObject;
    }

    getWarehouse(data: Object) {
        let warehouse = {};
        if (!Util.isEmpty(data)) {
          if (data.profile && !Util.isEmpty(data.profile) && data.profile.warehouses && !Util.isEmpty(data.profile.warehouses)) {
            const warehouseId = data.profile.warehouse_id;
            warehouse = this.getWarehouseById(warehouseId, data.profile.warehouses);
          } else if (data.pickup && !Util.isEmpty(data.pickup)) {
            warehouse = data.pickup;
          }
        }
        
        return warehouse;
    }
    
    getWarehouseLocation(warehouse: Object) {
        let location = {};
    
        if (!Util.isEmpty(warehouse)) {
          location = {
            latitude: Util.toDouble(warehouse.latitude),
            longitude: Util.toDouble(warehouse.longitude) 
          }
        }
    
        return location;
    }

    getWarehouseName(warehouseInfo: Object) {
        let warehouseName = "";
        if (warehouseInfo && !Util.isEmpty(warehouseInfo)) {
            const { name } = warehouseInfo;
            warehouseName = name;
        }
    
        return warehouseName;
    }

    getTravelMode(warehouseInfo: Object) {
        let travelMode = RIDE_MODE.DRIVING;
    
        if (warehouseInfo.ride_mode && warehouseInfo.ride_mode === RIDE_MODE.WALKING) {
          travelMode = RIDE_MODE.WALKING;
        }
    
        return travelMode;
    }

    getDistanceUnit(warehouseInfo: Object) {
      let unit = "";
        if (warehouseInfo && !Util.isEmpty(warehouseInfo) && warehouseInfo.away_distance_unit) {
          unit = warehouseInfo.away_distance_unit;
        }
    
        return unit;
    }

    getDistance(warehouseInfo: Object) {
        let distance = "";
        if (warehouseInfo && !Util.isEmpty(warehouseInfo) && warehouseInfo.away_distance) {
          distance = Util.getDistanceString(warehouseInfo.away_distance, this.getDistanceUnit(warehouseInfo));
        }
    
        return distance;
    }

    getDurationUnit(warehouseInfo: Object) {
      let unit = "";
        if (warehouseInfo && !Util.isEmpty(warehouseInfo) && warehouseInfo.away_duration_unit) {
          unit = warehouseInfo.away_duration_unit;
        }
    
        return unit;
    }
    
    getDuration(warehouseInfo: Object) {
        let duration = "";
        if (warehouseInfo && !Util.isEmpty(warehouseInfo) && warehouseInfo.away_duration) {
          duration = Util.getDurationString(warehouseInfo.away_duration, this.getDurationUnit(warehouseInfo));
        }
    
        return duration;
    }

    getGoogleRouteUrl(currentLocation: Object, warehouse: Object) {
        const source = MapRoutePresenter.getLocationString(currentLocation);
        const destLocation = this.getWarehouseLocation(warehouse);
        const destination = MapRoutePresenter.getLocationString(destLocation);
        const driveMode = this.getTravelMode(warehouse);
    
        const url = Util.getGoogleRouteUrl(source, destination, driveMode);
    
        return url;
    }

    getWarehouseNamePrefix(warehouseInfo: Object) {
      let title = "WH";
        if (warehouseInfo && !Util.isEmpty(warehouseInfo)) {
          const data = warehouseInfo.name.split(" ");
          title = data[0].charAt(0)
          if (data.length > 1) {
            title += data[1].charAt(0);
          }
        }
    
        return title;
    }

}

export default new WarehousePresenter();
