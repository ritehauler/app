// @flow
import React from "react";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";
import { Image, View } from "react-native";

import { Images } from "../../theme";
import { ButtonView } from "../../components";
import styles from "./styles";

export default class LeftViewNavigation extends React.PureComponent {
  static propTypes = {
    action: PropTypes.func,
    image: PropTypes.oneOfType([PropTypes.number, PropTypes.object])
  };

  static defaultProps = {
    action: () => Actions.pop(),
    image: Images.back
  };

  render() {
    const { action, image } = this.props;
    return (
      <ButtonView
        onPress={action}
        isBackgroundBorderLess
        style={styles.container}
      >
        <Image source={image} resizeMode="contain" />
      </ButtonView>
    );
  }
}
