// @flow
import _ from "lodash";
import React, { Component } from "react";
import { Keyboard, ScrollView, View } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { ServerRequestPage } from "../../components";
import { Strings } from "../../theme";
import styles from "./styles";

import DriverInfo from "./DriverInfo";
import DriverStatistics from "./DriverStatistics";
import DriverComplimentsList from "./DriverComplimentsList";
import EmptyList from "./EmptyList";

import {
  request as driverProfileRequest,
  resetDriverProfile
} from "../../actions/DriverProfileActions";

class DriverProfile extends Component {
  static propTypes = {
    driverProfileRequest: PropTypes.func.isRequired,
    driverProfile: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    resetDriverProfile: PropTypes.object.isRequired,
    driverId: PropTypes.number,
    driverName: PropTypes.string
  };

  static defaultProps = { driverId: -1, driverName: "" };

  constructor(props) {
    super(props);
    this.fetchData = this.fetchData.bind(this);
    this.renderContent = this.renderContent.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }
  componentDidMount() {
    this.fetchData();
  }

  componentWillUnmount() {
    this.props.resetDriverProfile();
  }

  fetchData() {
    const payload = {
      driver_id: this.props.driverId,
      mobile_json: 1
    };
    this.props.driverProfileRequest(payload);
  }

  _renderDriverInfo(data, isDetail) {
    return <DriverInfo data={data} isDetail={isDetail} />;
  }

  _renderDriverStatistics(data) {
    if (data.rating_options && data.rating_options.length > 0) {
      return <DriverStatistics data={data.rating_options} />;
    }
    return null;
  }

  _renderDriverComplimentsList(data) {
    const dataReviews = _.reject(data.reviews || [], ["review", ""]);

    if (dataReviews.length > 0) {
      return <DriverComplimentsList data={dataReviews} />;
    }
    return null;
    //return <EmptyList />;
  }

  renderContent() {
    const { data } = this.props.driverProfile;
    return (
      <ScrollView style={styles.container} bounces={false}>
        {this._renderDriverInfo(data, true)}
        {this._renderDriverStatistics(data)}
        {this._renderDriverComplimentsList(data)}
      </ScrollView>
    );
  }

  render() {
    const { networkInfo, driverProfile, driverName } = this.props;
    const { data, isFetching, errorMessage, failure } = driverProfile;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <View style={styles.container}>
        {_.isEmpty(data, true) &&
          this._renderDriverInfo({ full_name: driverName }, false)}
        <ServerRequestPage
          data={data}
          errorMessage={errorMessage}
          failure={failure}
          renderView={this.renderContent}
          isFetching={isFetching}
          isInternetConnected={isInternetConnected}
          fetchData={this.fetchData}
          emptyMessage={Strings.noDriverProfileFound}
        />
      </View>
    );
  }
}

const mapStateToProps = store => ({
  driverProfile: store.driverProfile,
  networkInfo: store.networkInfo
});
const actions = {
  driverProfileRequest,
  resetDriverProfile
};

export default connect(
  mapStateToProps,
  actions
)(DriverProfile);
