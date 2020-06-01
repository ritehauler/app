// @flow
import React from "react";
import Modal from "react-native-modal";
import { View, Image, ScrollView } from "react-native";
import moment from "moment";
import PropTypes from "prop-types";

import { Text, ButtonView, StatusBarIos, Separator } from "../../components";
import { SearchBar, GradientButton, BoxError } from "../../appComponents";
import { Images, Strings, Metrics, Colors } from "../../theme";
import styles from "./styles";

import FilterSlider from "./FilterSlider";
import FilterDate from "./FilterDate";
import OrderStatusList from "./OrderStatusList";

import orderStatusData from "../../sampleJson/orderStatus.json";

const FILTER_OPTIONS = "filterOptions";
const ORDER_LIST = "orderList";

export default class Filter extends React.PureComponent {
  static propTypes = {
    onFiltersSelected: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.show = this.show.bind(this);
    this.hide = this.hide.bind(this);
    this.reset = this.reset.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.onRightSearchPress = this.onRightSearchPress.bind(this);
    this.onPressItemStatusList = this.onPressItemStatusList.bind(this);
  }

  state = {
    isVisible: false,
    filterOptions: {}
  };

  onPressButton() {
    const searchOrderNumber = this.searchBar.getSearchText();
    const startDate = this.startDate.getDate();
    const endDate = this.endDate.getDate();
    const filterValues = this.filterSlider.getSliderValues();
    const isStartDateEmptyButNotEndDate = endDate !== "" && startDate === "";
    const isStartDateGreaterThanEndDate = moment(startDate) > moment(endDate);

    if (isStartDateEmptyButNotEndDate) {
      this.dateError.setMessageAndError(true, Strings.errorMessageStartDate);
    } else if (isStartDateGreaterThanEndDate) {
      this.dateError.setMessageAndError(
        true,
        Strings.errorMessageStartDateGreater
      );
    } else {
      this.dateError.setMessageAndError(false, "");

      const filterOptions = {};
      let filterCount = 0;
      let priceFilterAdded = false;
      if (searchOrderNumber !== "") {
        filterOptions.order_number = searchOrderNumber;
        filterCount += 1;
      }
      if (startDate !== "") {
        filterOptions.start_date = startDate;
        filterCount += 1;
      }
      if (endDate !== "") {
        filterOptions.end_date = endDate;
        filterCount += 1;
      }
      if (this.orderStatus !== "") {
        filterOptions.order_status = this.orderStatus;
        filterOptions.orderStatusTitle = this.orderStatusTitle;
        filterCount += 1;
      }
      if (filterValues.min) {
        filterOptions.start_amount = filterValues.min;
        filterCount += 1;
        priceFilterAdded = true;
      }
      if (filterValues.max) {
        filterOptions.end_amount = filterValues.max;
        if (!priceFilterAdded) {
          filterCount += 1;
        }
      }
      this.props.onFiltersSelected(filterOptions, filterCount);
      this.hide();
    }
  }

  onRightSearchPress() {
    this.selectedView =
      this.selectedView === FILTER_OPTIONS ? ORDER_LIST : FILTER_OPTIONS;
    if (this.selectedView === FILTER_OPTIONS) {
      this._scrollTo(0);
      this.setSearchBar(Images.arrowDownLight, Colors.text.searchLabel);
    } else {
      this._scrollTo();
      this.setSearchBar(Images.arrowDownDark, Colors.text.primary);
    }
  }

  onPressItemStatusList(data) {
    this.searchBar.setRightTextImageAndColor(
      data.title,
      Images.arrowDownLight,
      Colors.text.searchLabel
    );
    this.orderStatus = data.key;
    this.orderStatusTitle = data.title;
    this._scrollTo(0);
    this.selectedView = FILTER_OPTIONS;
  }

  setSearchBar(image, color) {
    this.searchBar.setRightImageAndColor(image, color);
  }

  show(filterOptions) {
    this.setState({
      isVisible: true,
      filterOptions
    });
  }

  hide() {
    this.setState({
      isVisible: false
    });
  }

  reset() {
    this.searchBar.setTextRightTextImageAndColor(
      "",
      Strings.selectStatus,
      Images.arrowDownLight,
      "searchLabel"
    );
    this.filterSlider.resetSlider();
    this.startDate.resetDate();
    this.endDate.resetDate();

    this.selectedView = FILTER_OPTIONS;
    this.orderStatus = "";
    this.orderStatusTitle = "";
    this._scrollTo(0);

    this.props.onFiltersSelected({}, 0);
    this.hide();
  }

  _scrollTo(x = Metrics.screenWidth, animated = false) {
    this.scrollView.scrollTo({ x, animated });
  }

  selectedView = FILTER_OPTIONS;
  orderStatus = "";
  orderStatusTitle = "";

  _renderNavBar() {
    return (
      <View style={styles.navBar}>
        <ButtonView style={styles.cross} onPress={this.hide}>
          <Image source={Images.cross} />
        </ButtonView>
        <Text style={styles.navTitle} type="bold2" size="large">
          {Strings.filter}
        </Text>
        <ButtonView style={styles.clear} onPress={this.reset}>
          <Text color="accent" size="small">
            {Strings.clear}
          </Text>
        </ButtonView>
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  _renderSearchBar() {
    const { filterOptions } = this.state;
    const searchText = filterOptions.order_number || "";
    const rightText = filterOptions.orderStatusTitle || Strings.selectStatus;
    return (
      <SearchBar
        hasRightView
        rightText={rightText}
        ref={ref => {
          this.searchBar = ref;
        }}
        searchText={searchText}
        rightImage={Images.arrowDownLight}
        rightTextColor="searchLabel"
        placeHolder={Strings.searchForOrderId}
        onRightViewPress={this.onRightSearchPress}
      />
    );
  }

  _renderDates() {
    const { filterOptions } = this.state;
    return (
      <View style={styles.datesContainer}>
        <FilterDate
          label={Strings.start}
          ref={ref => {
            this.startDate = ref;
          }}
          date={filterOptions.start_date || ""}
        />
        <FilterDate
          label={Strings.end}
          style={styles.endDate}
          ref={ref => {
            this.endDate = ref;
          }}
          date={filterOptions.end_date || ""}
        />
      </View>
    );
  }

  _renderSortByDate() {
    return (
      <View style={styles.sortByDateContainer}>
        <Text style={styles.sortByDate} type="semiBold" size="small">
          {Strings.sortByDate}
        </Text>
        {this._renderDates()}
      </View>
    );
  }

  _renderSortByAmount() {
    const { filterOptions } = this.state;
    const rest = {};
    if (filterOptions.start_amount) {
      rest.minValueSelected = filterOptions.start_amount;
    }
    if (filterOptions.end_amount) {
      rest.maxValueSelected = filterOptions.end_amount;
    }
    return (
      <FilterSlider
        ref={ref => {
          this.filterSlider = ref;
        }}
        {...rest}
      />
    );
  }

  _renderDateError() {
    return (
      <BoxError
        ref={ref => {
          this.dateError = ref;
        }}
        errorContainerStyle={styles.boxErrorContainerStyle}
        containerStyle={styles.boxContainerStyle}
      />
    );
  }

  _renderFilterOptions() {
    return (
      <View style={styles.filterOptions}>
        {this._renderSortByDate()}
        {this._renderDateError()}
        {this._renderSortByAmount()}
      </View>
    );
  }

  _renderOrderStatusList() {
    return (
      <OrderStatusList
        data={orderStatusData}
        onPressItem={this.onPressItemStatusList}
      />
    );
  }

  _renderContent() {
    return (
      <ScrollView
        horizontal
        pagingEnabled
        scrollEnabled={false}
        ref={ref => {
          this.scrollView = ref;
        }}
        showsHorizontalScrollIndicator={false}
        style={styles.content}
      >
        {this._renderFilterOptions()}
        {this._renderOrderStatusList()}
      </ScrollView>
    );
  }

  _renderButton() {
    return (
      <View style={styles.buttonContainer}>
        <GradientButton
          text={Strings.done}
          ref={ref => {
            this.gradientButton = ref;
          }}
          onPress={this.onPressButton}
        />
      </View>
    );
  }

  _renderStatusBarIos() {
    return <StatusBarIos />;
  }

  render() {
    const { isVisible } = this.state;
    return (
      <Modal
        isVisible={isVisible}
        onBackdropPress={this.hide}
        onBackButtonPress={this.hide}
        style={styles.modal}
      >
        <View style={styles.body}>
          {this._renderStatusBarIos()}
          {this._renderNavBar()}
          {this._renderSeparator()}
          {this._renderSearchBar()}
          {this._renderContent()}
          {this._renderButton()}
        </View>
      </Modal>
    );
  }
}
