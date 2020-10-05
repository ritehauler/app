//@flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import _ from "lodash";
import { ButtonView, Text } from "../../../components";
import styles from "./styles";
import { Images, ApplicationStyles } from "../../../theme";

class RateAgentItem extends Component {
  static propTypes = {
    index: PropTypes.number,
    rateAgentItem: PropTypes.object,
    onPress: PropTypes.func
  };

  static defaultProps = {
    index: undefined,
    rateAgentItem: {},
    onPress: () => {}
  };

  shouldComponentUpdate(nextProps, nextState) {
    return (
      !_.isEqual(nextProps, this.props) || !_.isEqual(nextState, this.state)
    );
  }

  renderCheckImage() {
    if (!this.props.rateAgentItem.isSelected) {
      return null;
    }

    return <Image source={Images.check} style={styles.imageStyle} />;
  }

  render() {
    const { onPress, rateAgentItem, index } = this.props;
    const { containerStyle, titleTextStyle } = styles;

    return (
      <ButtonView onPress={() => onPress(index)} style={containerStyle}>
        <Text style={[titleTextStyle, ApplicationStyles.re14Black]}>
          {rateAgentItem.title}
        </Text>
        {this.renderCheckImage()}
      </ButtonView>
    );
  }
}

export default RateAgentItem;
