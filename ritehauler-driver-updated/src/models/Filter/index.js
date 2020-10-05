// @flow
import React from "react";
import Modal from "react-native-modal";
import { View, Image, ScrollView } from "react-native";

import { Text, ButtonView, StatusBarIos, Separator } from "../../components";
import { SearchBar, GradientButton } from "../../appComponents";
import { Images, Strings, Metrics, Colors } from "../../theme";
import styles from "./styles";

import FilterSlider from "./FilterSlider";
import FilterDate from "./FilterDate";
import OrderStatusList from "./OrderStatusList";

import orderStatusData from "../../sampleJson/orderStatus.json";

const FILTER_OPTIONS = "filterOptions";
const ORDER_LIST = "orderList";

export default class Filter extends React.PureComponent {
  constructor(props) {
    super(props);
    this.show = this.show.bind(this);
    this.hide = this.hide.bind(this);
    this.reset = this.reset.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.onRightSearchPress = this.onRightSearchPress.bind(this);
    this.onPressItem = this.onPressItem.bind(this);
  }

  state = {
    isVisible: false
  };

  onPressButton() {
    this.hide();
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

  onPressItem(data) {
    this.searchBar.setRightTextImageAndColor(
      data.title,
      Images.arrowDownLight,
      Colors.text.searchLabel
    );
    this._scrollTo(0);
    this.selectedView = FILTER_OPTIONS;
  }

  setSearchBar(image, color) {
    this.searchBar.setRightImageAndColor(image, color);
  }

  show() {
    this.setState({
      isVisible: true
    });
  }

  hide() {
    this.setState({
      isVisible: false
    });
  }

  reset() {
    this.searchBar.setSearchText("");
    this.filterSlider.resetSlider();
    this.startDate.resetDate();
    this.endDate.resetDate();
  }

  _scrollTo(x = Metrics.screenWidth, animated = false) {
    this.scrollView.scrollTo({ x, animated });
  }

  selectedView = FILTER_OPTIONS;
  cityName = Strings.statusPending;

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
    return (
      <SearchBar
        hasRightView
        rightText={Strings.statusPending}
        ref={ref => {
          this.searchBar = ref;
        }}
        rightImage={Images.arrowDownLight}
        rightTextColor="searchLabel"
        placeHolder={Strings.searchForOrderId}
        onRightViewPress={this.onRightSearchPress}
      />
    );
  }

  _renderDates() {
    return (
      <View style={styles.datesContainer}>
        <FilterDate
          label={Strings.start}
          ref={ref => {
            this.startDate = ref;
          }}
        />
        <FilterDate
          label={Strings.end}
          style={styles.endDate}
          ref={ref => {
            this.endDate = ref;
          }}
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
    return (
      <FilterSlider
        ref={ref => {
          this.filterSlider = ref;
        }}
      />
    );
  }

  _renderFilterOptions() {
    return (
      <View style={styles.filterOptions}>
        {this._renderSortByDate()}
        {this._renderSortByAmount()}
      </View>
    );
  }

  _renderOrderStatusList() {
    return (
      <OrderStatusList data={orderStatusData} onPressItem={this.onPressItem} />
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
