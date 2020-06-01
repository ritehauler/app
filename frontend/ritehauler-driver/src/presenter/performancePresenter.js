// @flow
import { API_PERFORMANCE } from "../config/WebService";
import Util from "../util";
import { PERFORMANCE_TYPE, PERFORMANCE_VALUE , PERFORMANCE_KEY, PERFORMANCE_KEY_VALUE} from "../constant";
import { stringify } from "querystring";

class PerformancePresenter {

  sendPerformanceRequest(performanceRequest: any, startDate: any, endDate: any, period: String)  {
    const payload = {
      search: this.getPerformanceFilterArray(startDate, endDate),
      period,
      device_type: Util.getPlatform()
    };

    if (period === PERFORMANCE_TYPE.DAILY) {
      payload.order = "created_at";
      payload.sort = "asc";
    }

    performanceRequest(API_PERFORMANCE, payload);
  }

  getPerformanceFilterArray(startDate: any, endDate: any) {
    const filterData = [];
    if (startDate && !Util.isEmpty(startDate) && endDate && !Util.isEmpty(endDate)) {
      filterData.push("created_at,>=," + startDate + " 00:00:00");
      filterData.push("created_at,<=," + endDate + " 23:59:59");
    }

    return filterData;
  }

  getStatsDates(joinDate: any, recordLimit: number, period: string) {
    const dates = new Map();
    if (joinDate && !Util.isEmpty(joinDate)) {
      // const startDate = Util.statsFormatDate(joinDate);
      // const days = this.getDaysByPeriod(period);
      // const currentDate = Util.getCurrentDate();
      // const daysDifference = Util.getDateDifferenceInDays(startDate, currentDate);
      // let endDate = this.getEndDateByDays(currentDate, daysDifference, days);
      // const daysByPeriod = Util.toInt(daysDifference / days);
      // for (const i = 0; i < daysByPeriod; i++) {
      //   const dateRange = this.getDateRangeByDays(endDate, days);
      //   dates.set(i, dateRange);
      //   endDate = Util.getDateSubtractByDays(dateRange.startDate, 1);
      // }
      const days = this.getDaysByPeriod(period);
      const joinedDate = Util.statsFormatDate(joinDate);
      const currentDate = Util.getCurrentDate();
      let endDate = Util.getEndDateOfWeek(currentDate);
      const statsLimitDate = Util.getDateSubtractByMonth(endDate, recordLimit);
      let i = 0;
      
      while (!Util.isBeforeDate(endDate, statsLimitDate) && !Util.isBeforeDate(endDate, joinedDate)) {
        const dateRange = this.getDateRangeByDays(endDate, days);
        dates.set(i, dateRange);
        endDate = Util.getDateSubtractByDays(dateRange.startDate, 1);
        i++;
      }
    }

    return dates;
  }

  getEndDateByDays(currentDate: any, daysDifference: number, days: number) {

    const subtractDays = daysDifference % days;
    // const addDays = (days - subtractDays);
    // return Util.getDateAddByDays(currentDate, addDays);
    return Util.getDateSubtractByDays(currentDate, subtractDays + 1);
  }

  getDaysByPeriod(period: any) {
    let days = 0;
    switch(period) {
      case PERFORMANCE_TYPE.WEEKLY:
        days = PERFORMANCE_VALUE.WEEKLY;
        break;

      case PERFORMANCE_TYPE.DAILY:
        days = PERFORMANCE_VALUE.DAILY;
        break;

      default:
        days = PERFORMANCE_VALUE.WEEKLY;
        break;
    }

    return days;
  }

  getDateRangeByDays(endDate: any, days: number) {

    const startDate = Util.getDateSubtractByDays(endDate, days - 1);
    return this.getDateRangeObject(startDate, endDate);
  }

  getDateRangeObject(startDate: any, endDate: any) {
    return {
      startDate: Util.statsFormatDate(startDate),
      endDate: Util.statsFormatDate(endDate)
    };
  }

  getPerformanceTitleByKey(performanceKey: string) {

    let title = "";
    switch(performanceKey) {

      case PERFORMANCE_KEY.ORDERS_COMPLETED:
        title = PERFORMANCE_KEY_VALUE.ORDERS_COMPLETED;
        break;

      case PERFORMANCE_KEY.ORDERS_CANCELED:
        title = PERFORMANCE_KEY_VALUE.ORDERS_CANCELED;
        break;

      case PERFORMANCE_KEY.HOURS_ONLINE:
        title = PERFORMANCE_KEY_VALUE.HOURS_ONLINE;
        break;

      case PERFORMANCE_KEY.COMMISSION_EARNED:
        title = PERFORMANCE_KEY_VALUE.COMMISSION_EARNED;
        break;

      case PERFORMANCE_KEY.RATING:
        title = PERFORMANCE_KEY_VALUE.RATING;
        break;

      case PERFORMANCE_KEY.DELIVERIES_PERCENTAGE:
        title = PERFORMANCE_KEY_VALUE.DELIVERIES_PERCENTAGE;
        break;

      default:
        title = "";
        break;

    }

    return title;
  }

  isPerformed(title: string, currentValue: number, previousValue: number) {
    return (title === PERFORMANCE_KEY_VALUE.ORDERS_CANCELED) 
      ? (currentValue - previousValue) <= 0
      : (currentValue - previousValue) >= 0;
  }

  getValueByTitle(title: string, value: number, currencyCode: string) {
    let newValue = value;
    if (title === PERFORMANCE_KEY_VALUE.COMMISSION_EARNED) {
      newValue = Util.getCurrencyAmount(currencyCode, value);
    } else if (title === PERFORMANCE_KEY_VALUE.DELIVERIES_PERCENTAGE) {
      newValue = value + "%";
    } else if (title === PERFORMANCE_KEY_VALUE.HOURS_ONLINE) {
      newValue = Math.round(Util.secondsToHour(value));
    }

    return newValue;
  }

  getPerformanceObject(title: string, currentValue: any, previousValue: any, index: number) {
    return {
      id: index,
      title,
      value: currentValue,
      last_value: previousValue
    };
  }

  getPerformanceValueByKey(key: string, performance: any) {
    return !Util.isEmpty(performance) ? performance[key] : 0;
  }

  getPerformanceStats(current: Object, previous: Object) {
    const performanceStats = [];

    if (current && !Util.isEmpty(current)) {

      const currentArray = Object.keys(current);
      const previousArray = previous && !Util.isEmpty(previous) ? Object.keys(previous) : [];
      let index = 0;

      for (const key of currentArray) {
        const currentValue = this.getPerformanceValueByKey(key, current);
        const previousValue = this.getPerformanceValueByKey(key, previous);
        const title = this.getPerformanceTitleByKey(key);
        if (!Util.isEmpty(title)) {
          performanceStats.push(this.getPerformanceObject(title, currentValue, previousValue, index));
        }
        index++;
      }
    }

    return performanceStats;
  }

  getPerformanceDailyObject(day: string, performanceStats: any) {
    return {
      day,
      data: performanceStats
    };
  }

  getPreviousEndDate(performanceDaily: any, startDate: any) {
    const date = Util.statsFormatDate(performanceDaily[0].created_at);
    return  Util.isSameOrBeforeDate(startDate, date) ? {} : performanceDaily[0];
  }

  getUpdatedArrayByIndex(performanceDaily: any, index: number) {
    let updatedPerformance = performanceDaily;
    if (index > 0) {
      updatedPerformance = performanceDaily.slice(index);
    }

    return updatedPerformance;
  }

  getUpdatedPerformanceByStartDate(performance: any, startDate: any) {

    const previousDay = this.getPreviousEndDate(performance, startDate);
    const startIndex = Util.isEmpty(previousDay) ? 0 : 1;
    return this.getUpdatedArrayByIndex(performance, startIndex);
  }

  getPerformanceStatsDaily(performanceDaily: any, startDate: any) {
    const performanceStatsDaily = [];

    if (performanceDaily && !Util.isEmpty(performanceDaily)) {
      let previousDay = this.getPreviousEndDate(performanceDaily, startDate);
      const startIndex = Util.isEmpty(previousDay) ? 0 : 1;
      const updatedPerformance = this.getUpdatedArrayByIndex(performanceDaily, startIndex);

      for (const i = 0; i < updatedPerformance.length; i++) {
        const day = Util.statsDayFormat(updatedPerformance[i].created_at);
        const currentDay = updatedPerformance[i];
        const performanceStats = this.getPerformanceStats(currentDay, previousDay);
        performanceStatsDaily.push(this.getPerformanceDailyObject(day, performanceStats));
        previousDay = currentDay;
      }
    }

    return performanceStatsDaily;
  }

  getMaxValue(performanceList: any) {
    let max = 0;

    if (performanceList && !Util.isEmpty(performanceList)) {
      for (const performance of performanceList) {
        const value = Util.toDouble(performance[PERFORMANCE_KEY.COMMISSION_EARNED]);
        max = value > max ? value : max;
      }
    }

    return max;
  }

  getGraphXValues(period: string) {
    const xValues = [];
    const noOfDays = this.getDaysByPeriod(period);
    for (const i = 0; i < noOfDays; i++) {
      xValues.push(i + 1);
    }

    return xValues;
  }

  getGraphYValues(performance: any, period: string) {
    const yValues = [];

    if (performance && !Util.isEmpty(performance)) {

      const noOfDays = this.getDaysByPeriod(period);
      const maxValue = this.getMaxValue(performance);
      const factor =  maxValue / (noOfDays - 1);
      const roundOfFactor = Math.ceil(factor / 5) * 5;

      for (const i = 0; i < noOfDays; i++) {
        yValues.push(i * roundOfFactor);
      }
    }

    return yValues;
  }

  getGraphPoints(performances: any, xValues: any) {
    const points = [];

    if (performances && !Util.isEmpty(performances)) {

      for (const i = 0; i < performances.length; i++) {

        const performance = performances[i];
        const point = { x: xValues[i], y: Util.toDouble(performance[PERFORMANCE_KEY.COMMISSION_EARNED])}
        points.push(point);
      }
    }

    return points;
  }
}

export default new PerformancePresenter();
