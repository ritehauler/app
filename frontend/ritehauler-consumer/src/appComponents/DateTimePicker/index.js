// @flow
import React from "react";
import PropTypes from "prop-types";
import DatePicker from "react-native-datepicker";

import Utils from "../../util";
import styles from "./styles";
import { Strings } from "../../theme";

export default class DateTimePicker extends React.PureComponent {
  static propTypes = {
    dateValue: PropTypes.string.isRequired,
    displayFormat: PropTypes.string.isRequired,
    onChangeDate: PropTypes.func.isRequired,
    format: PropTypes.string.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onDateChange = this.onDateChange.bind(this);
  }

  state = {
    dateValue: this.props.dateValue
  };

  onDateChange = date => {
    this.setState({ dateValue: date });
    const { format, displayFormat, onChangeDate } = this.props;
    if (this.props.onChangeDate) {
      const displayDate = Utils.formatDate(date, format, displayFormat);
      onChangeDate(date, displayDate);
    }
  };

  showDateTimePicker = () => {
    this.datePicker.onPressDate();
  };

  render() {
    const { dateValue } = this.state;
    const { ...rest } = this.props;
    return (
      <DatePicker
        style={styles.datePicker}
        showIcon={false}
        date={dateValue}
        onDateChange={this.onDateChange}
        confirmBtnText={Strings.confirm}
        cancelBtnText={Strings.cancel}
        ref={ref => {
          this.datePicker = ref;
        }}
        {...rest}
      />
    );
  }
}
