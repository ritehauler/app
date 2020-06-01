// @flow
import React from "react";
import { Image } from "react-native";

import { Images } from "../../theme";

export default class CustomMarker extends React.PureComponent {
  render() {
    return <Image source={Images.knob} />;
  }
}
