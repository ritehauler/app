import React, { PureComponent } from "react";
import { connect } from "react-redux";
import { View, Text, StyleSheet } from "react-native";
import PropTypes from "prop-types";
import { ButtonView } from "../../components";
import { ApplicationStyles, Colors, Metrics } from "../../theme";
import {
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  STATUS_DECLINED,
  STATUS_ACCEPTED,
  AUTO_DECLINE_PERIOD
} from "../../constant";

// redux imports
import { request as updateAssignedOrderStatusRequest } from "../../actions/UpdateAssignedOrderStatus";

class Timer extends PureComponent {
  static propTypes = {
    time: PropTypes.number
  };

  static defaultProps = {
    time: AUTO_DECLINE_PERIOD
  };

  constructor(props) {
    super(props);
    this.state = {
      seconds: 0,
      minutes: props.time
    };
    this.timerID;
    this.autoDeclineOrder = this.autoDeclineOrder.bind(this);
    this.startTimer = this.startTimer.bind(this);
    this.restartTimer = this.restartTimer.bind(this);
    this.clearTimer = this.clearTimer.bind(this);
    this.acceptOrder = this.acceptOrder.bind(this);
    this.executeDeclineCallback = this.executeDeclineCallback.bind(this);
    this.searchOrderHistoryID = this.searchOrderHistoryID.bind(this);
  }

  componentDidMount() {
    this.startTimer();
  }

  componentWillUnmount() {
    this.clearTimer();
  }

  restartTimer(minutes) {
    this.setState(
      {
        minutes,
        seconds: 0
      },
      () => {
        this.clearTimer();
        this.startTimer();
      }
    );
  }

  startTimer() {
    this.timerID = setInterval(() => {
      if (this.state.seconds === 0 && this.state.minutes === 0) {
        this.clearTimer();
        this.autoDeclineOrder();
      } else {
        this.setState({
          seconds:
            this.state.minutes > 0
              ? this.state.seconds !== 0
                ? this.state.seconds - 1
                : 60
              : this.state.seconds > 0
                ? this.state.seconds - 1
                : 0,
          minutes:
            this.state.seconds === 0
              ? this.state.minutes > 0
                ? this.state.minutes - 1
                : 0
              : this.state.minutes
        });
      }
    }, 1000);
  }

  clearTimer() {
    if (this.timerID) {
      clearInterval(this.timerID);
      this.timerID = undefined;
    }
  }

  autoDeclineOrder(declineReason = "auto declined") {
    let payload = {
      entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
      order_status: STATUS_DECLINED,
      order_id: this.props.orderEntityID,
      driver_id: this.props.userEntityID,
      login_entity_id: this.props.userEntityID,
      login_entity_type_id: USER_ENTITY_TYPE_ID,
      comment: declineReason
    };
    this.searchOrderHistoryID(payload);
    this.props.updateAssignedOrderStatusRequest(payload);
  }

  acceptOrder() {
    let payload = {
      entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
      order_status: STATUS_ACCEPTED,
      order_id: this.props.orderEntityID,
      driver_id: this.props.userEntityID,
      login_entity_id: this.props.userEntityID,
      login_entity_type_id: USER_ENTITY_TYPE_ID
    };

    this.searchOrderHistoryID(payload);
    this.props.updateAssignedOrderStatusRequest(payload);
    this.clearTimer();
  }

  executeDeclineCallback() {
    this.props.cbOnDeclinePress();
  }

  searchOrderHistoryID(payload) {
    for (let i = 0; i < this.props.notificationListing.data.length; i++) {
      if (
        this.props.notificationListing.data[i].order_id ===
        this.props.orderEntityID
      ) {
        payload["entity_history_id"] = this.props.notificationListing.data[
          i
        ].entity_history_id;
      }
    }
  }

  render() {
    return (
      <View style={styles.timer}>
        <Text style={ApplicationStyles.dBoldB20}>{`${this.state.minutes} : ${
          this.state.seconds
        }`}</Text>
        <View style={styles.acceptDecline}>
          <ButtonView
            style={{ padding: Metrics.baseMargin }}
            onPress={this.acceptOrder}
          >
            <Text style={[ApplicationStyles.re13Black, styles.text]}>
              Accept
            </Text>
          </ButtonView>
          <View style={styles.separator} />
          <ButtonView
            style={{ padding: Metrics.baseMargin }}
            onPress={this.executeDeclineCallback}
          >
            <Text style={[ApplicationStyles.re13Black, styles.text]}>
              Decline
            </Text>
          </ButtonView>
        </View>
      </View>
    );
  }
}

const styles = StyleSheet.create({
  timer: {
    flexDirection: "row",
    width: Metrics.screenWidth,
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: Colors.background.primary,
    paddingLeft: Metrics.doubleBaseMargin,
    paddingRight: Metrics.baseMargin
  },
  acceptDecline: {
    flexDirection: "row",
    alignItems: "center"
  },
  separator: {
    width: 1,
    height: Metrics.ratio(20),
    backgroundColor: Colors.background.accent,
    marginHorizontal: Metrics.baseMargin
  },
  text: {
    color: Colors.text.orange
  }
});

const mapStateToProps = ({ notificationListing }) => ({ notificationListing });

const actions = {
  updateAssignedOrderStatusRequest
};

export default connect(mapStateToProps, actions, null, { withRef: true })(
  Timer
);
