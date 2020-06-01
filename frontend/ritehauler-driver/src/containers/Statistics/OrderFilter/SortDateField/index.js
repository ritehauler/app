// @flow
import React, { PureComponent } from "react";
import DatePicker from "react-native-datepicker";
import PropTypes from "prop-types";
import moment from "moment";
import { ButtonView, Text } from "../../../../components";
import { ApplicationStyles, Metrics } from "../../../../theme";
import styles from "./styles";
import Util from "../../../../util";
import { DATE_FORMAT } from "../../../../constant";

class SortDateField extends PureComponent {
  static propTypes = {
    title: PropTypes.string,
    date: PropTypes.string,
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    onDateChange: PropTypes.func
  };

  static defaultProps = {
    title: "",
    date: "1991-1-1",
    style: {},
    onDateChange: () => {}
  };

  state = {
    date: this.props.date
  };

  componentWillReceiveProps(nextProps) {
    this.setState({ date: nextProps.date });
  }

  onDateSelection = date => {
    const { onDateChange } = this.props;
    this.setState({ date });
    onDateChange(date);
  };

  render() {
    const { title, style, isSelected = false } = this.props;
    const { containerStyle, dateInput, dateText, dateTouchBody } = styles;
    const currentDate = moment().format(DATE_FORMAT);

    return (
      <ButtonView
        isBackgroundBorderLess
        onPress={() => this.datePicker.onPressDate()}
        style={[containerStyle, style]}
      >
        <Text
          style={[
            isSelected
              ? ApplicationStyles.sB14Black
              : ApplicationStyles.sB14Grey,
            { marginBottom: Metrics.smallMargin / 2 }
          ]}
        >
          {title}
        </Text>
        <DatePicker
          ref={ref => {
            this.datePicker = ref;
          }}
          date={this.state.date}
          mode="date"
          placeholder="placeholder"
          format={DATE_FORMAT}
          minDate={moment(currentDate, DATE_FORMAT)
            .subtract(6, "months")
            .format(DATE_FORMAT)}
          maxDate={currentDate}
          confirmBtnText="Confirm"
          cancelBtnText="Cancel"
          customStyles={{
            dateInput,
            dateText: isSelected
              ? ApplicationStyles.re14Black
              : ApplicationStyles.re13Gray,
            dateTouchBody
          }}
          showIcon={false}
          onDateChange={this.onDateSelection}
        />
      </ButtonView>
    );
  }
}

export default SortDateField;
