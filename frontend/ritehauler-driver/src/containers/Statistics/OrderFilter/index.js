// @flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import { Actions } from "react-native-router-flux";
import MultiSlider from "@ptomasroos/react-native-multi-slider";
import LinearGradient from "react-native-linear-gradient";
import ActionSheet from "react-native-actionsheet";
import { Images, Colors, ApplicationStyles, Metrics } from "../../../theme";
import {
  ImageTextSelector,
  Text,
  Separator,
  BottomButton,
  ButtonView
} from "../../../components";
import { SearchBar } from "../../../appComponents";
import styles from "./styles";
import { PAYMENT_METHOD, PRICE_RANGE, DATE_FORMAT } from "../../../constant";
import SortDateField from "./SortDateField";
import moment from "moment";
import Utils from "../../../util";

// redux imports
import { constructOrderStatusesArray } from "../../../reducers/reduxSelectors";

class OrderFilter extends Component {
  static propTypes = {
    filter: PropTypes.object,
    onFilter: PropTypes.func,
    currencyCode: PropTypes.string
  };

  static defaultProps = {
    filter: {},
    onFilter: () => {},
    currencyCode: ""
  };

  constructor(props) {
    super(props);
    this.orderMinValue = 5;
    this.orderMaxValue = 999;

    this.state = {
      startDate: moment()
        .startOf("isoWeek")
        .format(DATE_FORMAT),
      endDate: moment().format(DATE_FORMAT),
      isStartDateSelected: false,
      isEndDateSelected: false,
      multiSliderValue: [this.orderMinValue, this.orderMaxValue]
    };

    this.handleDoneButtonPress = this.handleDoneButtonPress.bind(this);
    this.handleClearPress = this.handleClearPress.bind(this);
    this.setOrderFilter = this.setOrderFilter.bind(this);
  }

  setOrderFilter(
    startDate = undefined,
    endDate = undefined,
    minAmount = undefined,
    maxAmount = undefined
  ) {
    let states = {};

    if (startDate) {
      states["startDate"] = startDate;
      states["isStartDateSelected"] = true;
    }

    if (endDate) {
      states["endDate"] = endDate;
      states["isEndDateSelected"] = endDate;
    }

    this.setState({ ...states, multiSliderValue: [minAmount, maxAmount] });
  }

  onStartDate = date => {
    this.setState({
      startDate: date,
      isStartDateSelected: true
    });
  };

  onEndDate = date => {
    this.setState({
      endDate: date,
      isEndDateSelected: true
    });
  };

  onPressClear = () => {
    Actions.pop();
  };

  multiSliderValuesChange = values => {
    this.setState({
      multiSliderValue: values
    });
  };

  orderMaxValue: number;
  orderMinValue: number;

  renderNavBar() {
    return (
      <View style={styles.navBar}>
        <ButtonView
          style={{
            padding: Metrics.baseMargin
          }}
          onPress={() => this.props.closeModal(false)}
        >
          <Image
            source={Images.cross}
            style={{ width: Metrics.icon.small, height: Metrics.icon.small }}
            resizeMode="contain"
          />
        </ButtonView>
        <View
          style={{
            flex: 1,
            alignItems: Utils.isPlatformAndroid() ? "flex-start" : "center",
            justifyContent: "center"
          }}
        >
          <Text
            style={[
              ApplicationStyles.dBoldB20,
              {
                paddingLeft: Utils.isPlatformAndroid()
                  ? Metrics.smallMargin + Metrics.baseMargin
                  : Metrics.smallMargin
              }
            ]}
          >
            Filter
          </Text>
        </View>
        <ButtonView
          style={{ padding: Metrics.baseMargin }}
          onPress={this.handleClearPress}
        >
          <Text style={ApplicationStyles.re16Orange}>Clear</Text>
        </ButtonView>
      </View>
    );
  }

  handleClearPress() {
    this.props.cbOnClear();
  }

  renderSortDate() {
    const { startDate, endDate } = this.state;

    return (
      <View style={styles.cardContainerStyle}>
        <Text
          style={[
            ApplicationStyles.sB16Black,
            { marginBottom: Metrics.smallMargin }
          ]}
        >
          Sort by Date
        </Text>
        <View style={styles.sortDateContainerStyle}>
          <SortDateField
            title="Start"
            date={startDate}
            style={styles.sortDateLeftStyle}
            onDateChange={this.onStartDate}
            isSelected={this.state.isStartDateSelected}
          />
          <SortDateField
            title="End"
            date={endDate}
            style={styles.sortDateRightStyle}
            onDateChange={this.onEndDate}
            isSelected={this.state.isEndDateSelected}
          />
        </View>
      </View>
    );
  }

  renderRangeSlider() {
    const {
      cardContainerStyle,
      sliderTitleContainerStyle,
      slideTitleTextStyle,
      trackFillStyle,
      trackEmptyStyle,
      markerStyle,
      pressedMarkerStyle
    } = styles;

    const { currencyCode } = this.props;
    const { multiSliderValue } = this.state;

    const customMarkers = (
      <LinearGradient
        colors={Colors.lgColArray}
        start={{ x: 0.0, y: 0 }}
        end={{ x: 0.8, y: 0 }}
        style={{
          height: Metrics.ratio(20),
          width: Metrics.ratio(20),
          borderRadius: Metrics.ratio(10)
        }}
      />
    );

    return (
      <View style={cardContainerStyle}>
        <View style={sliderTitleContainerStyle}>
          <Text style={[ApplicationStyles.sB16Black, { flex: 1 }]}>
            Sort by Amount
          </Text>
          <Text style={ApplicationStyles.re14Black}>{`$${
            multiSliderValue[0]
          } - $${multiSliderValue[1]}`}</Text>
        </View>
        <View
          style={{
            alignItems: "center",
            paddingTop: Metrics.baseMargin + Metrics.smallMargin
          }}
        >
          <MultiSlider
            values={[multiSliderValue[0], multiSliderValue[1]]}
            selectedStyle={trackFillStyle}
            unselectedStyle={trackEmptyStyle}
            markerStyle={markerStyle}
            containerStyle={{ height: Metrics.ratio(12) }}
            pressedMarkerStyle={pressedMarkerStyle}
            onValuesChange={this.multiSliderValuesChange}
            min={this.orderMinValue}
            max={this.orderMaxValue}
            step={PRICE_RANGE.STEP_VALUE}
            allowOverlap
            snapped
            customMarker={() => customMarkers}
          />
        </View>
      </View>
    );
  }

  renderSeparator() {
    return <Separator />;
  }

  renderCardSeparator = () => <View style={styles.cardSeparatorStyle} />;

  renderDoneButton() {
    return (
      <View style={styles.button}>
        <BottomButton title="DONE" onPress={this.handleDoneButtonPress} />
      </View>
    );
  }

  handleDoneButtonPress() {
    let activeFiltersCount = 1;

    if (this.state.isStartDateSelected) activeFiltersCount++;
    if (this.state.isEndDateSelected) activeFiltersCount++;

    if (this.state.isEndDateSelected && !this.state.isStartDateSelected) {
      alert("Start date is required");
    } else if (
      this.state.isStartDateSelected &&
      this.state.isEndDateSelected &&
      moment(this.state.endDate).isBefore(moment(this.state.startDate))
    ) {
      alert("End date can't be before start date");
    } else {
      this.props.closeModal(false);
      this.props.cbOnDone(
        this.state.isStartDateSelected ? this.state.startDate : undefined,
        this.state.isEndDateSelected ? this.state.endDate : undefined,
        this.state.multiSliderValue[0],
        this.state.multiSliderValue[1],
        activeFiltersCount
      );
    }
  }

  render() {
    return (
      <View style={styles.containerStyle}>
        {this.renderNavBar()}
        <View style={{ marginHorizontal: Metrics.baseMargin }}>
          {this.renderCardSeparator()}
          {this.renderSortDate()}
          {this.renderCardSeparator()}
          {this.renderRangeSlider()}
        </View>
        {this.renderDoneButton()}
      </View>
    );
  }
}

export default OrderFilter;
