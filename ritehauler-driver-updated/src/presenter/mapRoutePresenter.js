// @flow
import _ from "lodash";
import Polyline from "@mapbox/polyline";
import GeoLib from "geolib";
import { API_GOOGLE_DIRECTION, GOOGLE_API_KEY } from "../config/WebService";
import { RIDE_MODE } from "../constant";
import Util from "../util";
import { TripPresenter } from "../presenter";

class MapRoutePresenter {
  mapRouteRequest;

  init(mapRouteRequest) {
    this.mapRouteRequest = mapRouteRequest;
  }

  getRouteRequestData(currentLocation: Object, orders: any, warehouseInfo: Object = {}) {
    let payload = {};
    if (orders && !Util.isEmpty(orders)) {
      const travelMode = this.getTravelMode(orders[0]);
      const originLatLng = this.getRouteOrigin(currentLocation);
      let destinationLatLng = this.getWarehouseLocationString(warehouseInfo);
      let wayPointsLatLng = "";
      if (orders && !Util.isEmpty(orders)) {
        const sortedOrderList = this.sortOrdersByDistance(
          currentLocation,
          orders
        );
        const orderMap = TripPresenter.getOrderMapFromArray(sortedOrderList);
        destinationLatLng = this.getRouteDestination(orderMap);
        wayPointsLatLng = this.getRouteWayPoints(
          orderMap,
          destinationLatLng
        );
      }

      payload = {
        key: GOOGLE_API_KEY,
        mode: travelMode,
        origin: originLatLng,
        destination: destinationLatLng
      };

      if (!Util.isEmpty(wayPointsLatLng)) {
        payload.waypoints = wayPointsLatLng;
      }
    }

    return payload;
  }

  sendRouteRequest(currentLocation: Object, data: any) {
    if (data.trips && !Util.isEmpty(data.trips)) {
      const payload = this.getRouteRequestData(currentLocation, data.trips);
      if (!Util.isEmpty(payload)) {
        this.mapRouteRequest(API_GOOGLE_DIRECTION, payload);
      }
    }
  }

  getLocationFromPayload(payload: Object) {
    return {
      latitude: payload.latitude,
      longitude: payload.longitude
    };
  }

  getTravelMode(order: Object) {
    let travelMode = RIDE_MODE.DRIVING;

    if (order.ride_mode === RIDE_MODE.WALKING) {
      travelMode = RIDE_MODE.WALKING;
    }

    return travelMode;
  }

  getLocationString(location: Object) {
    let latlong = "";
    if (location && !Util.isEmpty(location)) {
      latlong = location.latitude + "," + location.longitude;
    }

    return latlong;
  }

  getWarehouseLocationString(warehouseInfo: Object) {
    let latlong = "";
    if (warehouseInfo && !Util.isEmpty(warehouseInfo)) {
      latlong = warehouseInfo.latitude + "," + warehouseInfo.longitude;
    }

    return latlong;
  }

  getRouteOrigin(location: Object) {
    return this.getLocationString(location);
  }

  getRouteDestination(orderMap: any) {
    return Array.from(orderMap.keys()).pop();
  }

  getRouteWayPoints(orderMap: any, destinationMarker: string) {
    let wayPoints  = "";
    if (!Util.isEmpty(orderMap) && orderMap.size > 1) {

      const ordersMap = _.cloneDeep(orderMap);
      ordersMap.delete(destinationMarker);
      const markers = ordersMap.keys();

      for (const marker of markers) {
        wayPoints += "via:" + marker + "|";
      }

      wayPoints = wayPoints.substring(0, wayPoints.length - 1);
    }

    return wayPoints;
  }

  getRoutePoints(routeData: Object) {
    let points = [];
    if (routeData && !Util.isEmpty(routeData)) {
      points = Polyline.decode(
        routeData.data.routes[0].overview_polyline.points
      );
    }

    return points;
  }

  getRouteCoordinates(routeData: Object) {
    let routeCoordinates = [];
    if (routeData && !Util.isEmpty(routeData)) {
      const routePoints = this.getRoutePoints(routeData);
      routeCoordinates = routePoints.map((point, index) => {
        return {
          latitude: point[0],
          longitude: point[1]
        };
      });
    }

    return routeCoordinates;
  }

  getDistance(sourceLocation: Object, destLocation: Object) {
    return GeoLib.getDistanceSimple(
      {
        latitude: sourceLocation.latitude,
        longitude: sourceLocation.longitude
      },
      {
        latitude: destLocation.latitude,
        longitude: destLocation.longitude
      }
    );
  }

  sortOrdersByDistance(currentLocation: Object, orderList: any) {
    const orders = _.cloneDeep(orderList);
    for (const order of orders) {
      const destLocation = TripPresenter.getDestinationLocation(order);
      const distance = this.getDistance(currentLocation, destLocation);
      order.distance = distance;
    }

    orders.sort((order1, order2) => order1.distance - order2.distance);
    return orders;
  }

  getGoogleRouteUrl(currentLocation: Object, order: Object) {
    const source = this.getLocationString(currentLocation);
    const destination = TripPresenter.getDestinationLocationString(order);
    const driveMode = this.getTravelMode(order);

    const url = Util.getGoogleRouteUrl(source, destination, driveMode);

    return url;
  }
}

export default new MapRoutePresenter();
