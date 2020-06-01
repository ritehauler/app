// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import moment from "moment";

import styles from "./styles";
import { Text, ButtonView } from "../../../components";
import { DateTimePicker } from "../../../appComponents";
import { DATE_FORMAT, DISPLAY_DATE_FORMAT } from "../../../constants";
import { Strings } from "../../../theme";
import Utils from "../../../util";

export default class FilterDate extends React.PureComponent {
  static propTypes = {
    label: PropTypes.string.isRequired,
    date: PropTypes.string,
    dateLabel: PropTypes.string,
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ])
  };

  static defaultProps = {
    style: {},
    date: "",
    dateLabel: Strings.selectDate
  };

  constructor(props) {
    super(props);
    this.onChangeDate = this.onChangeDate.bind(this);
    this.onPressDateView = this.onPressDateView.bind(this);
  }

  state = this._getDefaultDateObject();

  onPressDateView() {
    this.datePicker.showDateTimePicker();
  }

  onChangeDate = (date, displayDate) => {
    this.setState({ date, displayDate });
  };

  getDate = () =>
    this.state.displayDate === this.props.dateLabel ? "" : this.state.date;
  resetDate = () => {
    this.setState({
      date: moment().format(DATE_FORMAT),
      displayDate: this.props.dateLabel
    });
  };

  _getDefaultDateObject() {
    const dateString =
      this.props.date === "" ? moment().format(DATE_FORMAT) : this.props.date;
    const displayDate =
      this.props.date === ""
        ? this.props.dateLabel
        : Utils.formatDate(dateString, DATE_FORMAT, DISPLAY_DATE_FORMAT);
    return {
      date: dateString,
      displayDate
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
