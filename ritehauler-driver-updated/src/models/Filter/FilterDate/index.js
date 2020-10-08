// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import moment from "moment";

import styles from "./styles";
import { Text, ButtonView } from "../../../components";
import { DateTimePicker } from "../../../appComponents";
import { DATE_FORMAT, DISPLAY_DATE_FORMAT } from "../../../constants";
import Utils from "../../../util";

export default class FilterDate extends React.PureComponent {
  static propTypes = {
    label: PropTypes.string.isRequired,
    date: PropTypes.string,
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = { style: {}, date: moment().format(DATE_FORMAT) };

  constructor(props) {
    super(props);
    this.onChangeDate = this.onChangeDate.bind(this);
    this.onPressDateView = this.onPressDateView.bind(this);
  }

  state = this._getDefaultDateObject(this.props.date);

  onPressDateView() {
    this.datePicker.showDateTimePicker();
  }

  onChangeDate = (date, displayDate) => {
    this.setState({ date, displayDate });
  };

  resetDate = () => {
    this.setState(this._getDefaultDateObject(this.props.date));
  };

  _getDefaultDateObject(dateString) {
    return {
      date: dateString,
      displayDate: Utils.formatDate(
        dateString,
        DATE_FORMAT,
        DISPLAY_DATE_FORMAT
      )
    };
  }

  _renderDatePicker() {
    const { date } = this.state;
    return (
      <DateTimePicker
        mode="date"
        format={DATE_FORMAT}
        displayFormat={DISPLAY_DATE_FORMAT}
        dateValue={date}
        ref={ref => {
          this.datePicker = ref;
        }}
        onChangeDate={this.onChangeDate}
      />
    );
  }

  _renderContent() {
    const { label, style } = this.props;
    const { displayDate } = this.state;
    return (
      <ButtonView
        style={[styles.content, style]}
        onPress={this.onPressDateView}
      >
        <Text size="xSmall" type="semiBold">
          {label}
        </Text>
        <Text size="xSmall" style={styles.label}>
          {displayDate}
        </Text>
      </ButtonView>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderContent()}
        {this._renderDatePicker()}
      </View>
    );
  }
}
