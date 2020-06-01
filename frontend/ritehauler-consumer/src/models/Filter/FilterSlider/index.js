// @flow
import React from "react";
import { View } from "react-native";
import MultiSlider from "@ptomasroos/react-native-multi-slider";
import PropTypes from "prop-types";

import {
  MINIMUM_PRICE_FILTER,
  MAXIMUM_PRICE_FILTER,
  PRICE_STEP_FILTER
} from "../../../constants";
import { Metrics, Strings } from "../../../theme";
import styles from "./styles";
import { Text } from "../../../components";
import { CustomMarker } from "../../../appComponents";
import Utils from "../../../util";

export default class FilterSlider extends React.PureComponent {
  static propTypes = {
    minValue: PropTypes.number,
    maxValue: PropTypes.number,
    minValueSelected: PropTypes.number,
    maxValueSelected: PropTypes.number,
    step: PropTypes.number
  };

  static defaultProps = {
    minValue: MINIMUM_PRICE_FILTER,
    minValueSelected: MINIMUM_PRICE_FILTER,
    maxValue: MAXIMUM_PRICE_FILTER,
    maxValueSelected: MAXIMUM_PRICE_FILTER,
    step: PRICE_STEP_FILTER
  };

  constructor(props) {
    super(props);
    this.multiSliderValuesChange = this.multiSliderValuesChange.bind(this);
  }

  state = {
    multiSliderValue: [this.props.minValueSelected, this.props.maxValueSelected]
  };

  getSliderValues = () => {
    const { multiSliderValue } = this.state;
    const { minValue, maxValue } = this.props;
    const isMinValueSelected = multiSliderValue[0] === minValue;
    const isMaxValueSelected = multiSliderValue[1] === maxValue;
    const min =
      isMinValueSelected && isMaxValueSelected
        ? undefined
        : multiSliderValue[0];
    const max = isMaxValueSelected ? undefined : multiSliderValue[1];
    return { min, max };
  };

  resetSlider = () => {
    this.setState({
      multiSliderValue: [this.props.minValue, this.props.maxValue]
    });
  };

  multiSliderValuesChange = values => {
    this.setState({
      multiSliderValue: values
    });
  };

  _renderSlider() {
    const { minValue, maxValue, step } = this.props;
    const { multiSliderValue } = this.state;
    const sliderLength = Metrics.screenWidth - Metrics.baseMargin * 4.9;
    return (
      <MultiSlider
        values={[multiSliderValue[0], multiSliderValue[1]]}
        min={minValue}
        max={maxValue}
        step={step}
        allowOverlap={false}
        snapped
        customMarker={CustomMarker}
        selectedStyle={styles.multiSliderSelectedStyle}
        sliderLength={sliderLength}
        onValuesChange={this.multiSliderValuesChange}
        containerStyle={styles.multiSliderContainerStyle}
      />
    );
  }

  _renderValues() {
    const { multiSliderValue } = this.state;
    const plusString = multiSliderValue[1] === this.props.maxValue ? "+" : "";
    return (
      <View style={styles.valuesContainer}>
        <Text style={styles.title} type="semiBold" size="small">
          {Strings.sortByAmount}
        </Text>
        <Text size="xSmall">
          {Utils.getFormattedPrice(multiSliderValue[0])} -{" "}
          {Utils.getFormattedPrice(multiSliderValue[1])}
          {plusString}
        </Text>
      </View>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderValues()}
        {this._renderSlider()}
      </View>
    );
  }
}
