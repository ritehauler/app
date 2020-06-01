import React from "react";
import { Image } from "react-native";
import PropTypes from "prop-types";

import { ButtonView } from "../../../components";
import { Images } from "../../../theme";
import styles from "./styles";

export default class CurrentLocationIcon extends React.PureComponent {
  static propTypes = {
    onPress: PropTypes.func,
    hide: PropTypes.bool
  };

  static defaultProps = { onPress: undefined, hide: false };

  render() {
    const { onPress, hide } = this.props;
    if (hide) {
      return null;
    }
    return (
      <ButtonView
        onPress={onPress}
        disableRipple={!onPress}
        pointerEvents="box-only"
        style={styles.currentLocation}
      >
        <Image source={Images.currentLocation} />
      </ButtonView>
    );
  }
}
