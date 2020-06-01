// @flow
import React from "react";
import { View } from "react-native";
import moment from "moment";
import PropTypes from "prop-types";

import {
  TextInputContainer,
  DateTimeContainer,
  DateTimePicker,
  BoxError
} from "../../../appComponents";
import {
  DATE_FORMAT,
  DISPLAY_DATE_FORMAT,
  TIME_FORMAT,
  DISPLAY_TIME_FORMAT,
  DEFAULT_TIME,
  DATE_TIME_FORMAT,
  TIME_FORMAT_FULL
} from "../../../constants";
import styles from "./styles";
import { Strings } from "../../../theme";
import Utils from "../../../util";

export default class Footer extends React.PureComponent {
  static propTypes = {
    onLayoutChange: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.onPressPickTimeView = this.onPressPickTimeView.bind(this);
    this.onPressPickDateView = this.onPressPickDateView.bind(this);
    this.onChangeDate = this.onChangeDate.bind(this);
    this.onChangeTime = this.onChangeTime.bind(this);
  }

  onPressPickTimeView() {
    this.timePicker.showDateTimePicker();
  }

  onPressPickDateView() {
    this.datePicker.showDateTimePicker();
  }

  onChangeDate = (date, displayDate) => {
    this.date = date;
    this.pickDateView.setValue(displayDate);
    this.errorBoxRef[1].setShowError(false);
  };

  onChangeTime = (time, displayTime) => {
    this.time = time;
    this.pickTimeView.setValue(displayTime);
    this.errorBoxRef[0].setShowError(false);
  };

  getData = () => {
    const displayTime = this.pickTimeView.getValue();
    const displayDate = this.pickDateView.getValue();
    const order_notes = this.noteInputContainer.getText();
    if (displayTime === "") {
      this.errorBoxRef[0].setShowError(true);
    }
    if (displayDate === "") {
      this.errorBoxRef[1].setShowError(true);
    }

    const pickup_time = displayTime === "" ? "" : `${this.time}:00`;
    const pickup_date = displayDate === "" ? "" : this.date;
    const pickupDateTime =
      pickup_time !== "" && pickup_date !== ""
        ? `${pickup_date} ${pickup_time}`
        : "";

    const pickup_date_gmt =
      pickupDateTime !== ""
        ? Utils.getGmtDateTime(pickupDateTime, DATE_TIME_FORMAT, DATE_FORMAT)
        : "";

    const pickup_time_gmt =
      pickupDateTime !== ""
        ? Utils.getGmtDateTime(
            pickupDateTime,
            DATE_TIME_FORMAT,
            TIME_FORMAT_FULL
          )
        : "";

    /*
    return {
      pickup_time,
      pickup_date,
      pickup_date_gmt,
      pickup_time_gmt,
      order_notes
    };
    */
    return {
      pickup_time: pickup_time_gmt,
      pickup_date: pickup_date_gmt,
      order_notes
    };
  };

  /*
  date = moment()
    .add(1, "days")
    .format(DATE_FORMAT);
  minimumDate = moment()
    .add(1, "days")
    .format(DATE_FORMAT);
  */
  date = moment().format(DATE_FORMAT);
  minimumDate = moment().format(DATE_FORMAT);

  time = DEFAULT_TIME;

  errorBoxRef = [this.errorTime, this.errorDate];

  _renderPickTimeView() {
    return (
      <DateTimeContainer
        title={Strings.orderPickUpTime}
        value=""
        placeholder={Strings.selectTime}
        onPress={this.onPressPickTimeView}
        ref={ref => {
          this.pickTimeView = ref;
        }}
      />
    );
  }

  _renderPickDateView() {
    return (
      <DateTimeContainer
        title={Strings.orderPickupDate}
        value=""
        placeholder={Strings.selectDate}
        onPress={this.onPressPickDateView}
        ref={ref => {
          this.pickDateView = ref;
        }}
      />
    );
  }

  _renderAddNote() {
    return (
      <TextInputContainer
        ref={ref => {
          this.noteInputContainer = ref;
        }}
        customContainerStyle={styles.noteContainer}
        title={Strings.addNote}
        placeHolder={Strings.writeSomething}
        multiline
        onLayoutChange={this.props.onLayoutChange}
      />
    );
  }

  _renderTimePicker() {
    return (
      <DateTimePicker
        mode="time"
        format={TIME_FORMAT}
        displayFormat={DISPLAY_TIME_FORMAT}
        dateValue={this.time}
        ref={ref => {
          this.timePicker = ref;
        }}
        onChangeDate={this.onChangeTime}
      />
    );
  }

  _renderDatePicker() {
    return (
      <DateTimePicker
        mode="date"
        format={DATE_FORMAT}
        displayFormat={DISPLAY_DATE_FORMAT}
        minDate={this.minimumDate}
        dateValue={this.date}
        ref={ref => {
          this.datePicker = ref;
        }}
        onChangeDate={this.onChangeDate}
      />
    );
  }

  _renderErrorMessage(index, message) {
    return (
      <BoxError
        ref={ref => {
          this.errorBoxRef[index] = ref;
        }}
        errorMessage={message}
        errorContainer={styles.errorContainer}
        containerStyle={styles.containerStyle}
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderTimePicker()}
        {this._renderPickTimeView()}
        {this._renderErrorMessage(0, Strings.errorMessagePickupTime)}
        {this._renderDatePicker()}
        {this._renderPickDateView()}
        {this._renderErrorMessage(1, Strings.errorMessagePickupDate)}
        {this._renderAddNote()}
      </View>
    );
  }
}
