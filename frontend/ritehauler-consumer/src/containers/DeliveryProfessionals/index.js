// @flow
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { GradientButton, RightViewNavigation } from "../../appComponents";
import { Text, ServerRequestPage, Loader } from "../../components";
import styles from "./styles";
import { Strings } from "../../theme";
import Item from "./Item";

import { request as deliveryProfessionalsRequest } from "../../actions/DeliveryProfessionalsActions";
import { request as saveProfessionalRequest } from "../../actions/SaveProfessionalActions";
import { saveDeliveryProfessionalOrder } from "../../actions/OrderActions";
import { ENTITY_TYPE_ID_DELIVERY_PROFESSIONAL } from "../../config/WebService";

class DeliveryProfessionals extends Component {
  static propTypes = {
    deliveryProfessionalsRequest: PropTypes.func.isRequired,
    saveProfessionalRequest: PropTypes.func.isRequired,
    saveProfessional: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    deliveryProfessionals: PropTypes.object.isRequired,
    truckSelectedId: PropTypes.any.isRequired,
    saveDeliveryProfessionalOrder: PropTypes.func.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressButton = this.onPressButton.bind(this);
    this.fetchData = this.fetchData.bind(this);
    this.renderContent = this.renderContent.bind(this);
    this.skipDeliveryProfessional = this.skipDeliveryProfessional.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    Actions.refresh({
      right: () => (
        <RightViewNavigation
          text={Strings.skip}
          action={this.skipDeliveryProfessional}
        />
      )
    });
    this.fetchData();
  }

  componentWillReceiveProps(nextProps) {
    if (
      nextProps.saveProfessional.isFetching !==
      this.props.saveProfessional.isFetching
    ) {
      this.loader.setLoading(nextProps.saveProfessional.isFetching);
    }
  }

  shouldComponentUpdate(nextProps: Object) {
    return (
      nextProps.deliveryProfessionals.isFetching !==
      this.props.deliveryProfessionals.isFetching
    );
  }

  onPressButton() {
    // get data from props
    const { truckSelectedId } = this.props;

    // set data
    const selectedDeliveryProfessional = this.itemList.getSelectedDeliveryProfessional();
    const professional_id = selectedDeliveryProfessional.entity_id;

    // set payload
    const payload = {
      truck_selected_id: truckSelectedId,
      professional_id,
      mobile_json: 1
    };

    this.props.saveProfessionalRequest(payload, selectedDeliveryProfessional);
    /*
    // check if it same request
    if (_.isEqual(payload, this.props.saveProfessional.payLoad)) {
      Actions.orderSummary();
    } else {
      this.props.saveProfessionalRequest(payload, selectedDeliveryProfessional);
    }
    */
  }

  skipDeliveryProfessional() {
    this.props.saveDeliveryProfessionalOrder({}, { professional_id: "" });
    Actions.paymentMethod();
  }

  fetchData() {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_DELIVERY_PROFESSIONAL,
      order_by: "number_of_labour",
      sorting: "asc",
      status: 1,
      mobile_json: 1
    };
    this.props.deliveryProfessionalsRequest(payload);
  }

  _renderHeader() {
    return (
      <Text style={styles.header} size="xSmall" textAlign="center">
        {Strings.deliveryProfessionalHeader}
      </Text>
    );
  }

  _renderItem() {
    const { data } = this.props.deliveryProfessionals;
    return (
      <Item
        ref={ref => {
          this.itemList = ref;
        }}
        data={data}
      />
    );
  }

  _renderContent() {
    return (
      <ScrollView contentContainerStyle={styles.scroll}>
        {this._renderHeader()}
        {this._renderItem()}
      </ScrollView>
    );
  }

  _renderButton() {
    return (
      <GradientButton
        ref={ref => {
          this.gradientButton = ref;
        }}
        onPress={this.onPressButton}
        setKeyboardEvent={false}
      />
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

  renderContent() {
    return (
      <View style={styles.container}>
        {this._renderContent()}
        {this._renderButton()}
        {this._renderLoading()}
      </View>
    );
  }

  render() {
    const { networkInfo, deliveryProfessionals } = this.props;
    const { data, isFetching, errorMessage, failure } = deliveryProfessionals;
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
      />
    );
  }
}

const mapStateToProps = store => ({
  deliveryProfessionals: store.deliveryProfessionals,
  saveProfessional: store.saveProfessional,
  networkInfo: store.networkInfo
});
const actions = {
  deliveryProfessionalsRequest,
  saveProfessionalRequest,
  saveDeliveryProfessionalOrder
};

export default connect(
  mapStateToProps,
  actions
)(DeliveryProfessionals);
