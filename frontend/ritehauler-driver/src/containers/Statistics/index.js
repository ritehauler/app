// @flow
import React, { Component } from "react";
import { ScrollView, View } from "react-native";
import { connect } from "react-redux";
import moment from "moment";
import styles from "./styles";
import { Actions } from "react-native-router-flux";
import { Separator, LoadingRequest, EmptyView } from "../../components";
import { FilterIcon } from "../../appComponents";
import NavigationView from "./NavigationView";
import GraphView from "./GraphView";
import PerformanceView from "./PerformanceView";
import WeeklyPerformanceView from "./WeeklyPerformanceView";
import Modal from "../Modal";
import OrderFilter from "./OrderFilter";
import {
  PERFORMANCE_TYPE,
  DATE_FORMAT,
  DISPLAY_DATE_FORMAT,
  SHORT_DISPLAY_DATE_FORMAT
} from "../../constant";
import { Metrics, ApplicationStyles, Colors } from "../../theme";
import Util from "../../util";

// redux import
import { request as statisticsRequest } from "../../actions/StatisticsActions";
import { request as weeklyStatisticsRequest } from "../../actions/WeeklyStatisticsActions";
import { selectCachedLoginUser } from "../../reducers/reduxSelectors";

class Statistics extends Component {
  constructor(props) {
    super(props);

    this.state = {
      isPrevious: false,
      isNext: false,
      isFilterActive: false,
      startDate: moment()
        .startOf("isoWeek")
        .format(DATE_FORMAT),
      endDate: moment()
        .endOf("isoWeek")
        .format(DATE_FORMAT),
      minAmount: undefined,
      maxAmount: undefined
    };

    this.requestData = this.requestData.bind(this);
    this.onPressNext = this.onPressNext.bind(this);
    this.onPressPrevious = this.onPressPrevious.bind(this);
    this.handleFilterPress = this.handleFilterPress.bind(this);
    this.cbOnDone = this.cbOnDone.bind(this);
    this.cbOnClear = this.cbOnClear.bind(this);
  }

  componentDidMount() {
    // monthly stats
    this.requestData(this.state.startDate, this.state.endDate);
    // weekly stats
    this.props.weeklyStatisticsRequest({
      driver_id: this.props.driverID
    });
    Actions.refresh({
      right: () => (
        <FilterIcon
          ref={ref => (this.filterIcon = ref)}
          imagesArray={["filter"]}
          actions={[() => this.handleFilterPress(true)]}
        />
      )
    });
  }

  handleFilterPress(value) {
    if (this.modal) {
      this.modal.setModalVisible(value);
    }
    if (this.state.isFilterActive) {
      setTimeout(() => {
        if (this.orderFilter) {
          this.orderFilter.setOrderFilter(
            this.state.startDate,
            this.state.endDate,
            this.state.minAmount,
            this.state.maxAmount
          );
        }
      }, 500);
    }
  }

  requestData(
    startDate,
    endDate,
    minAmount = undefined,
    maxAmount = undefined
  ) {
    this.props.statisticsRequest({
      driver_id: this.props.driverID,
      start_date: startDate,
      end_date: endDate,
      min_amount: minAmount,
      max_amount: maxAmount
    });
  }

  renderSeparator() {
    return <Separator />;
  }

  renderNavigationView() {
    return (
      <View>
        <NavigationView
          isNext={this.state.isFilterActive ? false : true}
          isPrevious={this.state.isFilterActive ? false : true}
          onPressNext={this.onPressNext}
          onPressPrevious={this.onPressPrevious}
          title={`${moment(
            this.state.startDate || moment(moment()).subtract(7, "days"),
            DATE_FORMAT
          ).format(SHORT_DISPLAY_DATE_FORMAT)} - ${moment(
            this.state.endDate || moment(),
            DATE_FORMAT
          ).format(SHORT_DISPLAY_DATE_FORMAT)}`}
        />
        <Separator style={{ height: Metrics.ratio(0.5) }} />
      </View>
    );
  }

  onPressNext() {
    this.state.startDate = moment(this.state.startDate, DATE_FORMAT)
      .add(1, "weeks")
      .format(DATE_FORMAT);
    this.state.endDate = moment(this.state.endDate, DATE_FORMAT)
      .add(1, "weeks")
      .format(DATE_FORMAT);
    this.requestData(this.state.startDate, this.state.endDate);
  }

  onPressPrevious() {
    this.state.startDate = moment(this.state.startDate, DATE_FORMAT)
      .subtract(1, "weeks")
      .format(DATE_FORMAT);
    this.state.endDate = moment(this.state.endDate, DATE_FORMAT)
      .subtract(1, "weeks")
      .format(DATE_FORMAT);
    this.requestData(this.state.startDate, this.state.endDate);
  }

  renderGraphView() {
    return (
      <GraphView
        startDate={this.state.startDate}
        endDate={this.state.endDate}
        currencyCode={"$"}
      />
    );
  }

  renderPerformanceView() {
    return <PerformanceView currencyCode={"$"} />;
  }

  renderWeeklyPerformanceView() {
    return (
      <WeeklyPerformanceView
        startDate={this.state.startDate}
        endDate={this.state.endDate}
        currencyCode={"$"}
      />
    );
  }

  renderEmptyView() {
    return (
      <EmptyView
        video={Videos.emptyStatistics}
        text="No, Stats yet"
        description="Currently you haven't delivered any order."
        style={styles.containerStyle}
      />
    );
  }

  renderStatsView(isData) {
    if (isData) {
      return (
        <View>
          {this.renderGraphView()}
          {this.renderPerformanceView()}
          {!this.state.isFilterActive && this.renderWeeklyPerformanceView()}
        </View>
      );
    }

    return this.renderEmptyView();
  }

  cbOnDone(startDate, endDate, minAmount, maxAmount, activeFiltersCount) {
    this.setState({
      startDate: startDate
        ? Util.getGMTDate(`${startDate} 00:00:00`)
        : undefined,
      endDate: endDate ? Util.getGMTDate(`${endDate} 23:59:59`) : undefined,
      minAmount: minAmount,
      maxAmount: maxAmount,
      isFilterActive: true
    });
    this.filterIcon.setShowIndicator(true, activeFiltersCount);
    this.requestData(startDate, endDate, minAmount, maxAmount);
  }

  cbOnClear() {
    this.setState(
      {
        startDate: moment()
          .startOf("isoWeek")
          .format(DATE_FORMAT),
        endDate: moment()
          .endOf("isoWeek")
          .format(DATE_FORMAT),
        minAmount: undefined,
        maxAmount: undefined,
        isFilterActive: false
      },
      () => {
        this.filterIcon.setShowIndicator(false);
        this.requestData(this.state.startDate, this.state.endDate);
        this.handleFilterPress(false);
      }
    );
  }

  render() {
    if (this.props.isFetching) {
      return <LoadingRequest />;
    }

    return (
      <ScrollView
        bounces={false}
        contentContainerStyle={{
          paddingBottom: this.state.isFilterActive ? Metrics.baseMargin : 0,
          backgroundColor: Colors.background.login
        }}
      >
        {this.renderNavigationView()}
        {this.renderSeparator()}
        {this.renderStatsView(true)}
        <Modal ref={ref => (this.modal = ref)}>
          <OrderFilter
            ref={ref => (this.orderFilter = ref)}
            closeModal={this.handleFilterPress}
            cbOnDone={this.cbOnDone}
            cbOnClear={this.cbOnClear}
          />
        </Modal>
      </ScrollView>
    );
  }
}

const mapStateToProps = ({ user, statistics, weeklyStatistics }) => ({
  driverID: selectCachedLoginUser(user.data).entity_id,
  isFetching: statistics.isFetching || weeklyStatistics.isFetching
});

const actions = { statisticsRequest, weeklyStatisticsRequest };

export default connect(
  mapStateToProps,
  actions
)(Statistics);
