// @flow
import React from "react";
import { View } from "react-native";
import MultiSlider from "@ptomasroos/react-native-multi-slider";
import PropTypes from "prop-types";

import { Metrics } from "../../../theme";
import styles from "./styles";
import { Text } from "../../../components";
import { CustomMarker } from "../../../appComponents";

export default class FilterSlider extends React.PureComponent {
  static propTypes = {
    minValue: PropTypes.number,
    maxValue: PropTypes.number,
    step: PropTypes.number
  };

  static defaultProps = { minValue: 10, maxValue: 1000, step: 10 };

  constructor(props) {
    super(props);
    this.multiSliderValuesChange = this.multiSliderValuesChange.bind(this);
  }

  state = {
    multiSliderValue: [this.props.minValue, this.props.maxValue]
  };

  getSliderValues = () => {
    const min = this.state.multiSliderValue[0];
    const max = this.state.multiSliderValue[1];
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
    return (
      <View style={styles.valuesContainer}>
        <Text style={styles.title} type="semiBold" size="small">
          Sort by Amount
        </Text>
        <Text size="xSmall">
          ${multiSliderValue[0]} - ${multiSliderValue[1]}
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
