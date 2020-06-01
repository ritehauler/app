// @flow
import React from "react";
import PropTypes from "prop-types";
import Image from "react-native-image-progress";
import ProgressBar from "react-native-progress/Bar";

import { Colors, Images } from "../../theme";

export default class ImageServer extends React.PureComponent {
  static propTypes = {
    // placeholderSource
    source: PropTypes.oneOfType([PropTypes.object, PropTypes.number]).isRequired
  };

  _renderError() {
    return <Image source={Images.placeholder} />;
  }

  render() {
    const { ...rest } = this.props;
    return (
      <Image
        indicator={ProgressBar}
        renderError={err => this._renderError()}
        indicatorProps={{
          color: Colors.background.accent
        }}
        {...rest}
      />
    );
  }
}
