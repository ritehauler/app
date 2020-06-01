// @flow
import _ from "lodash";
import React, { Component } from "react";
import ScrollableTabView, {
  DefaultTabBar
} from "react-native-scrollable-tab-view";
import { View, Keyboard, Image } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { GradientButton, EmptyView } from "../../appComponents";
import { ButtonView, ServerRequestPage, Loader } from "../../components";
import vehicleDataHelper from "../../dataHelper/vehicleDataHelper";
import orderPlaceDataHelper from "../../dataHelper/orderPlaceDataHelper";
import styles from "./styles";
import { Strings, Metrics, Images } from "../../theme";
import Item from "./Item";

import { request as suggestedVehiclesRequest } from "../../actions/SuggestedVehiclesActions";
import { request as saveVehicleRequest } from "../../actions/SaveVehicleActions";

class SelectVehicle extends Component {
  static propTypes = {
    suggestedVehiclesRequest: PropTypes.func.isRequired,
    saveVehicleRequest: PropTypes.func.isRequired,
    networkInfo: PropTypes.object.isRequired,
    suggestedVehicles: PropTypes.object.isRequired,
    saveVehicle: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    // bind functions
    this._renderVehicle = this._renderVehicle.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.renderDefaultTabBar = this.renderDefaultTabBar.bind(this);
    this.fetchData = this.fetchData.bind(this);
    this.renderContent = this.renderContent.bind(this);
    this.onChangeTab = this.onChangeTab.bind(this);
    this.renderEmptyView = this.renderEmptyView.bind(this);

    // set vehicle payload
    this.setVehiclePayload(props);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    this.fetchData();
  }

  componentWillReceiveProps(nextProps) {
    if (
      nextProps.saveVehicle.isFetching !== this.props.saveVehicle.isFetching
    ) {
      this.loader.setLoading(nextProps.saveVehicle.isFetching);
    }
  }

  shouldComponentUpdate(nextProps: Object) {
    return (
      nextProps.suggestedVehicles.isFetching !==
      this.props.suggestedVehicles.isFetching
    );
  }

  onPressButton() {
    // get data from props
    const { data, truckSuggestedId } = this.props.suggestedVehicles;

    // set data
    const selectedVehicle = data[this.currentTab];
    const entityId = vehicleDataHelper.getEntityId(selectedVehicle);
    const truck_suggested_id = truckSuggestedId;

    // set  payload
    const payload = {
      truck_suggested_id,
      weight: this.payloadSelectVehicle.weight,
      volume: this.payloadSelectVehicle.volume,
      truck_id: entityId,
      mobile_json: 1
    };

    // check if it same request
    if (_.isEqual(payload, this.props.saveVehicle.payLoad)) {
      Actions.deliveryProfessional({
        truckSelectedId: this.props.saveVehicle.truckSelectedId
      });
    } else {
      this.props.saveVehicleRequest(payload, selectedVehicle);
    }
  }

  onChangeTab({ i }) {
    this.currentTab = i;
  }

  setVehiclePayload(props) {
    this.payloadSelectVehicle = orderPlaceDataHelper.getPayLoadSaveVehicleForOrder(
      props.orderInfo
    );
  }

  fetchData() {
    const { payLoad } = this.props.suggestedVehicles;
    if (!_.isEqual(payLoad, this.payloadSelectVehicle)) {
      this.props.suggestedVehiclesRequest(this.payloadSelectVehicle);
    }
  }

  currentTab = 0;

  _renderButton() {
    return (
      <GradientButton
        text={Strings.continueButton}
        ref={ref => {
          this.gradientButton = ref;
        }}
        onPress={this.onPressButton}
        setKeyboardEvent={false}
      />
    );
  }

  _renderVehicle(item, index) {
    return (
      <Item
        tabLabel={vehicleDataHelper.getTitle(item)}
        key={`${vehicleDataHelper.getEntityId(item)}_tab`}
        data={item}
      />
    );
  }

  _renderScrollTabView() {
    const { data } = this.props.suggestedVehicles;
    return (
      <ScrollableTabView
        style={styles.scrollTabs}
        tabBarUnderlineStyle={styles.tabBarUnderlineStyle}
        renderTabBar={this.renderDefaultTabBar}
        prerenderingSiblingsNumber={1}
        onChangeTab={this.onChangeTab}
      >
        {data.map(this._renderVehicle)}
      </ScrollableTabView>
    );
  }

  _renderLoading() {
    return (
      <Loader
        ref={ref => {
          this.loader = ref;
        }}
      />
    );
  }

  renderDefaultTabBar() {
    return (
      <DefaultTabBar
        tabStyle={styles.tabStyle}
        style={styles.defaultTabBarStyle}
        renderTab={this.renderTab}
        tabSpacing={Metrics.baseMargin * 2}
      />
    );
  }

  renderTab = (name, page, isTabActive, onPressHandler) => {
    const { data } = this.props.suggestedVehicles;
    return (
      <ButtonView
        style={styles.tabView}
        key={name}
        accessible
        enableClick
        accessibilityLabel={name}
        accessibilityTraits="button"
        onPress={() => onPressHandler(page)}
      >
        <Image
          style={styles.tabImage}
          source={{
            uri: vehicleDataHelper.getTabImage(data, page)
          }}
          resizeMode="contain"
        />
      </ButtonView>
    );
  };

  renderContent() {
    return (
      <View style={styles.container}>
        {this._renderScrollTabView()}
        {this._renderButton()}
        {this._renderLoading()}
      </View>
    );
  }

  renderEmptyView() {
    return (
      <EmptyView
        image={Images.emptyVehicle}
        title={Strings.noVehicleFound}
        description={Strings.noVehicleDescription}
      />
    );
  }

  render() {
    const { networkInfo, suggestedVehicles } = this.props;
    const { data, isFetching, errorMessage, failure } = suggestedVehicles;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <ServerRequestPage
        data={data}
        errorMessage={errorMessage}
        failure={failure}
        renderView={this.renderContent}
        isFetching={isFetching}
        isInternetConnected={isInternetConnected}
        fetchData={this.fetchData}
        emptyMessage={Strings.noVehicleFound}
        emptyView={this.renderEmptyView}
      />
    );
  }
}

const mapStateToProps = store => ({
  suggestedVehicles: store.suggestedVehicles,
  saveVehicle: store.saveVehicle,
  orderInfo: store.orderInfo,
  networkInfo: store.networkInfo
});
const actions = { suggestedVehiclesRequest, saveVehicleRequest };

export default connect(
  mapStateToProps,
  actions
)(SelectVehicle);
