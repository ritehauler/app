// @flow
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { GradientButton, LeftViewNavigation } from "../../appComponents";
import { ServerRequestPage, Loader, BackHandler } from "../../components";
import styles from "./styles";
import { Strings } from "../../theme";
import { Message } from "../../models";
import Utils from "../../util";

import Header from "./Header";
import ReviewsList from "./ReviewsList";
import RatingContainer from "./RatingContainer";
import Feedback from "./Feedback";

import { request as ratingOptionsList } from "../../actions/RatingOptionActions";
import { request as rateDriverRequest } from "../../actions/RateDriverActions";
import {
  ENTITY_TYPE_REVIEW_LIST,
  ENTITY_TYPE_ID_CUSTOMER,
  ENTITY_TYPE_ID_ORDER
} from "../../config/WebService";

class RateDriver extends Component {
  static propTypes = {
    rateDriverRequest: PropTypes.func.isRequired,
    ratingOptionsList: PropTypes.func.isRequired,
    networkInfo: PropTypes.object.isRequired,
    ratingOptions: PropTypes.object.isRequired,
    rateDriver: PropTypes.object.isRequired,
    user: PropTypes.object.isRequired,
    orderId: PropTypes.number
  };

  static defaultProps = { orderId: -1 };

  constructor(props) {
    super(props);
    this.onLayoutChange = this.onLayoutChange.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.renderContent = this.renderContent.bind(this);
    this.handleBackPress = this.handleBackPress.bind(this);
    this.discardOrder = this.discardOrder.bind(this);
    this.fetchData = this.fetchData.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    Actions.refresh({
      left: () => <LeftViewNavigation action={this.handleBackPress} />
    });
    this.fetchData();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.rateDriver.isFetching !== this.props.rateDriver.isFetching) {
      this.loader.setLoading(nextProps.rateDriver.isFetching);
    }
  }

  shouldComponentUpdate(nextProps: Object) {
    return (
      nextProps.ratingOptions.isFetching !== this.props.ratingOptions.isFetching
    );
  }

  onLayoutChange() {
    if (this.layoutChangeFirstTime) {
      this.layoutChangeFirstTime = false;
      return;
    }
    this.scrollView.scrollToEnd();
  }

  onPressButton() {
    const reviewList = this.reviewList.getReviewsList();
    const feedback = this.feedback.getFeedback();
    const rating = this.ratingContainer.getRating();
    if (rating === 0) {
      Utils.alert(Strings.errorMessageRating);
    } else if (reviewList.length === 0) {
      Utils.alert(Strings.errorMessageReview);
    } else {
      const reviewsOptionSelected = reviewList.toString();
      this.sendRequestRateDriver(rating, feedback, reviewsOptionSelected);
    }
    /*
    else if (feedback === "") {
      Utils.alert(Strings.errorMessageFeedback);
    }
    */
  }

  handleBackPress() {
    const { currentScene } = Actions;
    if (this.discardModel && currentScene === "rateDriver") {
      this.discardModel.show();
      return true;
    }
    return false;
  }

  discardOrder() {
    this.discardModel.hide();
    Actions.popTo("consumerLocation");
  }

  sendRequestRateDriver(rating, feedback, reviewsOptionSelected) {
    const payload = {
      target_entity_type_id: ENTITY_TYPE_ID_ORDER,
      actor_entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      target_entity_id: this.props.orderId,
      actor_entity_id: this.props.user.entity_id,
      rating,
      review: feedback,
      json_data: reviewsOptionSelected,
      mobile_json: 1
    };
    this.props.rateDriverRequest(payload);
  }

  fetchData() {
    const payload = {
      attribute_code: ENTITY_TYPE_REVIEW_LIST,
      mobile_json: 1
    };
    this.props.ratingOptionsList(payload);
  }

  layoutChangeFirstTime = true;

  _renderButton() {
    return (
      <GradientButton
        text={Strings.done}
        ref={ref => {
          this.gradientButton = ref;
        }}
        onPress={this.onPressButton}
      />
    );
  }

  _renderList() {
    const { data } = this.props.ratingOptions;
    return (
      <ReviewsList
        data={data}
        ref={ref => {
          this.reviewList = ref;
        }}
      />
    );
  }

  _renderTitle(text) {
    return <Header text={text} />;
  }

  _renderRating() {
    return (
      <RatingContainer
        ref={ref => {
          this.ratingContainer = ref;
        }}
      />
    );
  }

  _renderFeedback() {
    return (
      <Feedback
        onLayoutChange={this.onLayoutChange}
        ref={ref => {
          this.feedback = ref;
        }}
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

  _renderView() {
    return (
      <ScrollView
        style={styles.scroll}
        contentContainerStyle={styles.contentContainerStyle}
        ref={ref => {
          this.scrollView = ref;
        }}
      >
        {this._renderRating()}
        {this._renderTitle(Strings.review)}
        {this._renderList()}
        {this._renderTitle(Strings.feedback)}
        {this._renderFeedback()}
      </ScrollView>
    );
  }

  _renderDiscardModal() {
    return (
      <Message
        ref={ref => {
          this.discardModel = ref;
        }}
        description={Strings.confirmMessageDoNoRateDriver}
        rightButtonTitle={Strings.yes}
        onPress={this.discardOrder}
        leftButtonTitle={Strings.noThanks}
        isCancelable
      />
    );
  }

  _renderBackHandler() {
    return <BackHandler onBackPress={this.handleBackPress} />;
  }

  renderContent() {
    return (
      <View style={styles.container}>
        {this._renderView()}
        {this._renderButton()}
        {this._renderLoading()}
      </View>
    );
  }

  render() {
    const { networkInfo, ratingOptions } = this.props;
    const { data, isFetching, errorMessage, failure } = ratingOptions;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <View style={{ flex: 1 }}>
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
        {this._renderBackHandler()}
        {this._renderDiscardModal()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  ratingOptions: store.ratingOptions,
  rateDriver: store.rateDriver,
  user: store.user.data,
  networkInfo: store.networkInfo
});
const actions = {
  ratingOptionsList,
  rateDriverRequest
};

export default connect(
  mapStateToProps,
  actions
)(RateDriver);
