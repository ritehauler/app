import { createSelector } from "reselect";
import _ from "lodash";
import moment from "moment";
import Polyline from "@mapbox/polyline";
import {
  DISPLAY_DATE_FORMAT,
  ENTITY_TYPE_ORDER_ITEM,
  GMT_TIME_FORMAT
} from "../constant";
import Utils from "../util";

export const selectorTracking = location => location.tracking;
export const selectorLocation = location => location.coords;
export const selectorBackgroundLocation = location =>
  location.backgroundLocation;

export const selectLoginUser = user => {
  const { auth, gallery } = user;
  return auth && gallery
    ? {
        entity_id: user.entity_id,
        entity_auth_id: user.entity_auth_id,
        image: gallery.length ? gallery[0].file : undefined,
        thumb: gallery.length ? gallery[0].thumb : undefined,
        first_name: user.first_name,
        last_name: user.last_name,
        email: auth.email,
        address: user.address,
        mobile_no: auth.mobile_no,
        shift: user.shift,
        on_duty: +user.on_duty.value
        //login_status: user.login_status,
        //ride_status: user.ride_status,
        //user_status: user.user_status
      }
    : {};
};

export const selectCachedLoginUser = createSelector([selectLoginUser], user => {
  return {
    ...user
  };
});

export const selectMarkers = assignedOrdersData => {
  if (assignedOrdersData.length) {
    let markers = [];
    for (let i = 0; i < assignedOrdersData.length; i++) {
      const { order_pickup, order_dropoff, customer_id } = assignedOrdersData[
        i
      ];
      if (order_pickup && order_dropoff && customer_id) {
        markers.push({
          order_pickup: {
            latitude: parseFloat(order_pickup[0].latitude),
            longitude: parseFloat(order_pickup[0].longitude),
            address: order_pickup[0].address
          },
          order_dropoff: {
            latitude: parseFloat(order_dropoff[0].latitude),
            longitude: parseFloat(order_dropoff[0].longitude),
            address: order_dropoff[0].address
          },
          name: customer_id.value,
          image: Utils.getUserImage({ ...customer_id.detail })
        });
      }
    }
    return markers;
  }
  return [];
};

export const selectOrderItems = orderItemsArray => {
  if (orderItemsArray.length) {
    let orderItems = [];
    for (let i = 0; i < orderItemsArray.length; i++) {
      const {
        is_expensive,
        item_name,
        quantity,
        height,
        weight,
        width,
        length,
        volume,
        charge_extra_item,
        per_extra_item_charge
      } = orderItemsArray[i];
      orderItems.push({
        title: item_name,
        dimensions: `${width}x${height}x${length}, ${weight}${
          weight > 1 ? " pounds" : " pound"
        }, ${volume} cu inches`,
        dollar: +is_expensive.value ? true : false,
        quantity,
        isExtraItem: charge_extra_item,
        perExtraItemCharge: per_extra_item_charge
      });
    }
    return orderItems;
  }
  return [];
};

export const splitInToExtraItems = items => {
  if (items.length) {
    const extraItems = items.filter((item, index) => item.isExtraItem);
    const baseItems = items.filter((item, index) => !item.isExtraItem);

    return {
      extraItems,
      baseItems
    };
  }
  return {
    baseItems: [],
    extraItems: []
  };
};

export const constructPendingSectionListData = data => {
  if (!_.isEmpty(data)) {
    let sectionData = [];
    for (var key in data) {
      sectionData.push({
        title: key,
        key: key,
        data: data[key]
      });
    }
    return sectionData;
  }

  return [
    {
      data: []
    }
  ];
};

export const constructCreateOrderSectionList = (items, extraItems) => {
  const obj = {
    title: "Add Items",
    key: "addItems",
    data: items
  };
  const objExtra = {
    title: "Extra Items",
    key: "extraItems",
    data: extraItems
  };

  let sections = [];
  sections.push(obj);
  if (extraItems.length) {
    sections.push(objExtra);
  }
  return sections;
};

export const selectAddedExtraItems = items => {
  function imageFetcher(images) {
    return images.map(item => item.attachment.attachment_id);
  }

  let addedExtraItems = [];
  let itemImages = [];
  if (items.length) {
    for (let i = 0; i < items.length; i++) {
      addedExtraItems.push({
        ...items[i],
        item_images: imageFetcher(items[i].item_images).join(),
        entity_type_id: ENTITY_TYPE_ORDER_ITEM,
        item_receipt: items[i].is_expensive
          ? imageFetcher(items[i].item_receipt).join()
          : undefined
      });
    }
    return addedExtraItems;
  }
  return [];
};

export const selectAssignedOrderDetail = order => {
  const {
    order_pickup,
    order_dropoff,
    customer_id,
    order_status,
    pickup_date,
    pickup_time,
    grand_total,
    order_number
  } = order;
  const { detail } = customer_id;
  return {
    order_pickup: order_pickup[0],
    order_dropoff: order_dropoff[0],
    image: Utils.getUserImage({ ...detail }),
    name: customer_id.value,
    status: order_status.value,
    pickupDate: pickup_date,
    pickupTime: pickup_time,
    grand_total,
    order_number
  };
};

export const selectTruckDetails = order => {
  const { truck_id, driver_id, vehicle_id } = order;
  const { detail } = truck_id;

  return {
    driverName: driver_id.value,
    truck: `${detail.title} (${vehicle_id.detail.title})`,
    baseFee: detail.base_fee,
    perMin: detail.charge_per_minute,
    noPlate: vehicle_id.detail.vehicle_code,
    minimum: "--",
    maxEstimatedCharges: order.max_estimated_charges,
    minEstimatedCharges: order.min_estimated_charges,
    totalDistance: order.total_distance ? `${order.total_distance} mi` : "0 mi",
    totalMinutes: order.total_minutes ? `${order.total_minutes} min` : "0 min"
  };
};

export const selectDeliveryDetails = order => {
  const { professional_id, loading_price } = order;
  return {
    deliveryProfessionalsCount: professional_id.value,
    loadingPrice: loading_price
  };
};

export const selectPolylineCoordinates = data => {
  if (data.routes && data.routes.length && data.routes[0].overview_polyline) {
    const points = Polyline.decode(data.routes[0].overview_polyline.points);
    return points.map(point => {
      return {
        latitude: point[0],
        longitude: point[1]
      };
    });
  }
};

export const constructDriversMonthlyStats = data => {
  if (data) {
    let stats = [
      {
        id: 0,
        title: "Total Orders",
        value: data.current.total_order,
        last_value: data.previous.total_order,
        score: data.current.total_order
      },
      {
        id: 1,
        title: "Driver Declined",
        value: data.current.decline_order,
        last_value: data.previous.decline_order,
        score: data.current.decline_order
      },
      {
        id: 2,
        title: "Earned",
        value: data.current.total_earned,
        last_value: data.previous.total_earned,
        score: `$ ${data.current.total_earned}`
      },
      {
        id: 3,
        title: "Driver Canceled",
        value: data.current.cancelled_order,
        last_value: data.previous.cancelled_order,
        score: data.current.cancelled_order
      },
      {
        id: 4,
        title: "Rating",
        value: data.current.rating,
        last_value: data.previous.rating,
        score: data.current.rating
      },
      {
        id: 5,
        title: "Delivered",
        value: data.current.completed_order,
        last_value: data.previous.completed_order,
        score: data.current.completed_order
      }
    ];
    return stats;
  }

  return [];
};

export const constructDriverWeeklyStats = currentPreviousWeeks => {
  const current = currentPreviousWeeks
    ? currentPreviousWeeks.current
    : undefined;
  const previous = currentPreviousWeeks
    ? currentPreviousWeeks.previous
    : undefined;
  if (current && previous) {
    let stats = [];
    for (const item in current) {
      stats.push({
        day: `${item.substring(0, 3)}`,
        data: [
          {
            id: 0,
            title: "Total Orders",
            value: previous[item].total_order,
            score: current[item].total_order
          },
          {
            id: 1,
            title: "Driver Declined",
            value: previous[item].decline_order,
            score: current[item].decline_order
          },
          {
            id: 2,
            title: "Earned",
            value: previous[item].total_earned,
            score: `$ ${current[item].total_earned}`
          },
          {
            id: 3,
            title: "Driver Canceled",
            value: previous[item].cancelled_order,
            score: current[item].cancelled_order
          },
          {
            id: 4,
            title: "Rating",
            value: previous[item].rating,
            score: current[item].rating
          },
          {
            id: 5,
            title: "Delivered",
            value: previous[item].completed_order,
            score: current[item].completed_order
          }
        ]
      });
    }
    return stats;
  }
  return [];
};

export const constructGraphData = data => {
  if (data.graph && data.graph.title && data.graph.total) {
    const { title, total } = data.graph;

    // generating table of the biggest number than its division by 10 because number received from api can be arbitrary
    // so for plotting we are taking biggest value so every other value received can be fit in range produced by table.
    const generateTable = value => {
      let table = [];

      const minimumYValue = Math.round(Math.min(...total));
      const tableNumber = Math.round(Math.max(...total) / 10);
      if (minimumYValue < tableNumber) {
        table.push(minimumYValue);
      }

      for (let i = 1; i <= 10; i++) {
        table.push(value * i);
      }
      return table;
    };

    const generateXAxisPoints = value => {
      let xAxisPoints = [];
      for (let j = 1; j <= value; j++) {
        xAxisPoints.push(j);
      }
      return xAxisPoints;
    };

    /* these are just number or points */
    const xAxisPoints = title.length ? generateXAxisPoints(title.length) : [];

    return {
      /* xValues are labels of every points displayed on xAxis*/
      xValues: title.length ? title.map(item => item.toString()) : [],
      yValues: total.length
        ? generateTable(Math.round(Math.max(...total) / 10))
        : [],
      xAxisPoints,
      points: title.length
        ? title.map((item, index) => {
            return { x: xAxisPoints[index], y: total[index] };
          })
        : [],
      filterBy: data.graph.filter_by || ""
    };
  }
  return {
    xValues: ["1", "2", "3", "4", "5"],
    yValues: [],
    xAxisPoints: [1, 2, 3, 4, 5],
    points: []
  };
};

export const constructOrderStatusesArray = data => {
  let statuses = [];
  for (const item of data) {
    statuses.push(item.title);
  }
  statuses.push("Cancel");
  return statuses;
};

export const calculateDeclineTime = ordersArray => {
  console.log("assigned order detail : ", ordersArray);
  const orderUTC = moment.utc(ordersArray[0].date_assigned_at).toDate();
  const orderUtcTimeInLocalUtc = moment(orderUTC)
    .local()
    .format(GMT_TIME_FORMAT);
  const currentUtcTimeInLocalUtc = moment
    .utc()
    .local()
    .format("YYYY-MM-DD HH:mm:ss");
  const orderGraceTime = ordersArray[0].order_accept_grace_min;
  const timeDifference = moment(currentUtcTimeInLocalUtc).diff(
    orderUtcTimeInLocalUtc,
    "minutes"
  );

  return {
    timeDifference:
      orderGraceTime - timeDifference > 0 ? orderGraceTime - timeDifference : 0,
    isOrderValid: timeDifference > orderGraceTime ? false : true
  };
};
